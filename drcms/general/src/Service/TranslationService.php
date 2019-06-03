<?php

namespace Drupal\general\Service;

use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service for all translation issues.
 *
 * @package Drupal\general\Service
 * @category Service
 * @author R. Hasan <hasan@company.com>
 * @since 28.02.2017
 */
class TranslationService {

  /**
   * @var AccountInterface
   */
  public $currentUser;

  /**
   * @var LanguageManagerInterface
   */
  public $languageManager;

  /**
   * @var EntityFormBuilderInterface
   */
  public $entityFormBuilder;

  /**
   * @var ContentTranslationManagerInterface
   */
  public $contentTranslationManager;

  /**
   * @var EntityTypeManagerInterface
   */
  public $entityTypeManager;


  /**
   * TranslationService constructor.
   *
   * @param AccountInterface $currentUser
   * @param LanguageManagerInterface $languageManager
   * @param ContentTranslationManagerInterface $contentTranslationManager
   * @param EntityFormBuilderInterface $entityFormBuilder
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(
    AccountInterface $currentUser,
    LanguageManagerInterface $languageManager,
    ContentTranslationManagerInterface $contentTranslationManager,
    EntityFormBuilderInterface $entityFormBuilder,
    EntityTypeManagerInterface $entityTypeManager) {

    $this->currentUser = $currentUser;
    $this->languageManager = $languageManager;
    $this->contentTranslationManager = $contentTranslationManager;
    $this->entityFormBuilder = $entityFormBuilder;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * It will disable all untranslatable fields, which are not translatable. It will show only translatable fields and
   * therefore easier for the user/editor to keep overview on the translate form.
   *
   * @param $entity the current entity object
   * @param $form a reference variable of the current form
   */
  public function ShowTranslatableFields($entity, &$form) {

    if(!$entity->isDefaultTranslation()) {
      foreach ($entity->getFieldDefinitions() as $fieldName => $fieldDefinition) {
        if(!$fieldDefinition->isTranslatable() && isset($form[$fieldName])) {
          $form[$fieldName]['#access'] = false;
        }
      }
    }
  }

  /**
   * This method will display the proper form for translation. It will display the following forms:
   * - if target and source language are the same -> entity edit form
   * - if target language does exist as translation -> translation edit form
   * - if target language does not exist as translation -> translation add from
   *
   * During the development I took inspiration from the
   * class \Drupal\content_translation\Controller\ContentTranslationController
   * It helped me a lot to solve the problem with forms and to understand how Drupal does behave. So please if
   * you want to understand my code, take a look to the controller ContentTranslationController
   *
   * @param $id
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $entity_type_id
   * @param null $targetLanguage
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getTranslationForm($id, Request $request, $entity_type_id, $targetLanguage = null) {

    //all available languages as array
    $languages = $this->languageManager->getLanguages();
    //set the default language -> 'en'
    $defaultLanguageId = $this->languageManager->getDefaultLanguage()->getId();

    //if target language is not valid, then set the default language as target language
    if(!$this->validateTargetLanguage($languages, $targetLanguage)) {
      //set target language to 'en' -> this is our default language
      $targetLanguage = $defaultLanguageId;
    }

    /** @var ContentEntityInterface $entity */
    $entity = $this->entityTypeManager->getStorage($entity_type_id)->load($id);

    //set the page title
    $this->setPageTitle($entity, $request, $languages[$targetLanguage]);

    //all available translations as array
    $translations = $entity->getTranslationLanguages();

    $content = [];
    if ($this->languageManager->isMultilingual()) {

      // @todo Provide a way to figure out the default form operation. Maybe like
      //   $operation = isset($info['default_operation']) ? $info['default_operation'] : 'default';
      //   See https://www.drupal.org/node/2006348.
      $operation = 'default';


      //if target language the same as the source/original one -> do print the default edit form
      if($targetLanguage === $defaultLanguageId) {
        $content = $this->entityFormBuilder->getForm($entity);
      }

      //if a translation of the target language does exist -> display update translation form
      elseif($this->hasTranslation($translations, $targetLanguage)) {
        $form_state_additions = [];
        $form_state_additions['langcode'] = $targetLanguage;
        $form_state_additions['content_translation']['translation_form'] = false;

        $content = $this->entityFormBuilder->getForm($entity, $operation, $form_state_additions);
      }

      //if there is no translation for the selected target language -> display add translation form
      else {
        //Check if the current entity has the translation functionality activated
        if($this->contentTranslationManager->isSupported($entity->getEntityTypeId())) {
          // @todo Exploit the upcoming hook_entity_prepare() when available.
          // See https://www.drupal.org/node/1810394.
          $this->prepareTranslation($entity, $this->currentUser->id(), $languages[$defaultLanguageId], $languages[$targetLanguage]);

          $form_state_additions = [];
          $form_state_additions['langcode'] = $targetLanguage;
          $form_state_additions['content_translation']['source'] = $languages[$defaultLanguageId];
          $form_state_additions['content_translation']['target'] = $languages[$targetLanguage];
          $form_state_additions['content_translation']['translation_form'] = !$entity->access('update');

          $content = $this->entityFormBuilder->getForm($entity, $operation, $form_state_additions);
        } else {
          //Print an error to the log messages
          $message = "You should activate the translation functionality first!" .
            " Go to: [ Configuration > Regional and Language > Content language and translation > " .
            $entity->getEntityTypeId() . " ] Or just go to this URL: [ /admin/config/regional/content-language ]";
          \Drupal::logger($entity->getEntityTypeId())->error($message);
        }
      }
    }

    //return the rendered content
    return render($content);

  }

  /**
   * It will set the page title for the translate forms
   *
   * @param $entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $language
   */
  protected function setPageTitle($entity, Request $request, $language) {
    $title = $entity->label() . ' [' . $language->getName() . ' translation]';
    if ($route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT)) {
      $route->setDefault('_title', $title);
    }
  }

  /**
   * to return the current page title
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return null
   */
  protected function getPageTitle(Request $request) {
    if ($route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT)) {
      return \Drupal::service('title_resolver')->getTitle($request, $route);
    }

    return null;
  }

  /**
   * Populates target values with the source values.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity being translated.
   * @param integer $userId
   * @param \Drupal\Core\Language\LanguageInterface $source
   *   The language to be used as source.
   * @param \Drupal\Core\Language\LanguageInterface $target
   *   The language to be used as target.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function prepareTranslation(ContentEntityInterface $entity,
                                     $userId,
                                     LanguageInterface $source,
                                     LanguageInterface $target) {

    /**
     * NOTE: We have removed the source text from the target translation regarding
     *       this ticket: (CMS-637) Empty texts fields for new translations
     *
     * Delete call: $source_translation = $entity->getTranslation($source->getId());
     * Replace: $source_translation->toArray() with []
     */

    /* @var \Drupal\Core\Entity\ContentEntityInterface $source_translation */
    //$source_translation = $entity->getTranslation($source->getId());
    $target_translation = $entity->addTranslation($target->getId(), [] /*$source_translation->toArray()*/);

    // Make sure we do not inherit the affected status from the source values.
    if ($entity->getEntityType()->isRevisionable()) {
      $target_translation->setRevisionTranslationAffected(NULL);
    }

    /** @var \Drupal\user\UserInterface $user */
    $user = $this->entityTypeManager->getStorage('user')->load($userId);
    $metadata = $this->contentTranslationManager->getTranslationMetadata($target_translation);

    // Update the translation author to current user, as well the translation
    // creation time.
    $metadata->setAuthor($user);
    $metadata->setCreatedTime(REQUEST_TIME);
  }

  /**
   * It checks if the traget language has translation or not
   *
   * @param $translations
   * @param $targetLanguage
   * @return bool
   */
  protected function hasTranslation($translations, $targetLanguage) {
    return isset($translations[$targetLanguage]) ?: false;
  }

  /**
   * It checks if the target language is valid or not
   *
   * @param $languages
   * @param $targetLanguage
   * @return bool
   */
  protected function validateTargetLanguage($languages, $targetLanguage) {
    return ( $targetLanguage && isset($languages[$targetLanguage]) ) ? true : false;
  }

}