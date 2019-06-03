<?php

namespace Drupal\account\Form\my_entity;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\general\Service\TranslationService;
use Drupal\general\Helper\MainHelper;
use Drupal\general\Service\FormService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for MyEntity edit forms.
 *
 * @ingroup account
 */
class MyEntityForm extends ContentEntityForm {

  protected $translationService;
  protected $formService;
  
  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('arc.general.translation_service'),
      $container->get('arc.general.form_service')
    );
  }

  /**
   * MyEntityForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   * @param \Drupal\general\Service\TranslationService $translationService
   * @param \Drupal\general\Service\FormService $formService

   */
  public function __construct(
    EntityManagerInterface $entity_manager,
    TranslationService $translationService,
    FormService $formService) {

    parent::__construct($entity_manager);
    $this->translationService = $translationService;
    $this->formService = $formService;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\account\Entity\MyEntity */
    $form = parent::buildForm($form, $form_state);
  
    $form['#attributes']['class'][] = 'arc-form';
    $form['#attached']['library'][] = 'company/forms';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['actions']['#attributes']['class'][] = 'arc-bottom-fixed-form-actions';

    $form_mode = $form_state->getStorage()['form_display']->getMode();
    $form = $this->formService->getConditionalNewFields($form);

    if (!$this->entity->isNew()) {
      $form['new_revision'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => true,
        '#weight' => 10,
        '#access' => false
      );
      $form = $this->formService->setRevisionMessage($this->entity, $form, $form_mode);
      $this->formService->setAccountReadOnly($form, [
        'num_navi_products_man',
        'num_navi_products_all',
        'num_calc_top_products',
        'num_top_products_all',
        'num_set_top_products_all',
      ]);
    }
    
    $this->translationService->ShowTranslatableFields($this->entity, $form);

    if( $form_mode == 'default') {

      // Build a dynamic/virtual select option field for first letter filter in the form
      $form = $this->formService->buildFirstLetterFilter(
        $form,
        $form_state,
        MainHelper::FILTER,
        'filter_relation',
        '1st_letter');

      //Unset markers_set and xxxs_set
      $form = $this->formService->unsetMarkersSet($form, $this->entity->getEntityTypeId());
      $form = $this->formService->unsetProjectsSet($form, $this->entity->getEntityTypeId());
			$form = $this->formService->unsetAccessStatus($form, $this->entity->getEntityTypeId());
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionUserId(\Drupal::currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label MyEntity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label MyEntity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.my_entity.canonical', ['my_entity' => $entity->id()]);
  }
}
