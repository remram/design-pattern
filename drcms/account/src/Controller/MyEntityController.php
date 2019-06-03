<?php

namespace Drupal\account\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\account\Entity\my_entity\MyEntityInterface;
use Drupal\general\Helper\MainHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\general\Service\TranslationService;
use Drupal\general\Service\ViewService;

/**
 * Class MyEntityController.
 *
 *  Returns responses for MyEntity routes.
 *
 * @package Drupal\account\Controller
 */
class MyEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\general\Service\TranslationService $translationService
   */
  protected $translationService;

  /**
   * @var ViewService
   */
  protected $viewService;

  /**
   * MyEntityController constructor.
   *
   * @param \Drupal\general\Service\TranslationService $translationService
   * @param \Drupal\general\Service\ViewService $viewService
   */
  public function __construct(TranslationService $translationService, ViewService $viewService){
    $this->translationService = $translationService;
    $this->viewService = $viewService;
  }

  /**
   * @param ContainerInterface $container
   * @return static
   */
  public static function create (ContainerInterface $container){
    return new static (
      $container->get('arc.general.translation_service'),
      $container->get('arc.general.view_service')
    );
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function my_entityGroups($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getReferencedGroupsView($my_entity, MainHelper::MY_ENTITY);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function getFilter($my_entity, Request $request){
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getByContextualFilter(
      $my_entity,
      MainHelper::MY_ENTITY,
      'filter_relation',
      MainHelper::MY_ENTITY . '_filter_by_id',
      'filter_by_id_embed',
      $request);
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function addFilter($my_entity, Request $request){
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getFilterView(
      $my_entity,
      MainHelper::MY_ENTITY,
      'filter_relation',
      MainHelper::MY_ENTITY . '_filter_page',
      'filter_page',
      $request);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function my_entityAddress($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountAddressView(MainHelper::MY_ENTITY, $my_entity);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function my_entityContents($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountContentView(
      $my_entity,
      MainHelper::MY_ENTITY,
      MainHelper::MY_ENTITY_CONTENT_EMBED);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addContentForm($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountAddContentForm(
      $my_entity,
      MainHelper::MY_ENTITY,
      MainHelper::MY_ENTITY_CONTENT
      );
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function my_entityMenu($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    $this->viewService->redirectResponse($request);
    return $this->viewService->getAccountMenuForm($my_entity);
  }

  /**
   * @param $my_entity
   * @param null $target_language
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return array
   */
  public function my_entityTranslation($my_entity, $target_language = null, Request $request) {
    $this->viewService->redirectResponse($request);
    return [
      '#type' => 'markup',
      '#markup' => $this->translationService->getTranslationForm($my_entity, $request, MainHelper::MY_ENTITY, $target_language)
    ];
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getObjectProduct($my_entity, Request $request){
    return $this->getObject($my_entity, $request, 'product');
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getCollection($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountObjectCollectionView($my_entity, 'collection');
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getRelatedObjectProduct($my_entity, Request $request){
    return $this->getObject($my_entity, $request, 'product', 'related');
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getRelatedObjectStory($my_entity, Request $request){
    return $this->getObject($my_entity, $request, 'yyy', 'related');
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getRelatedObjectFairEdition($my_entity, Request $request){
    return $this->getObject($my_entity, $request, 'fair_edition', 'related');
  }

  /**
   * @param $id
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $entityType
   * @param null $type
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getObject($id, Request $request, $entityType, $type=null){
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $id);
    return $this->viewService->getAccountObjectView($id, $entityType, $type);
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addProduct($my_entity, Request $request){
    return $this->addObject($my_entity, 'product', $request);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return void
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function orderMyEntityProductsByName($my_entity, Request $request){
    $storage = $this->entityTypeManager()->getStorage(MainHelper::PRODUCT);
    $objectEntities = $storage->loadByProperties(['owner__target_id' => $my_entity]);

    $this->viewService->orderEntitiesByName($objectEntities, 'list_order_by_account');

    $destination = $request->query->get('destination');
    $this->viewService->redirectToRoute($destination);
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addProject($my_entity, Request $request){
    return $this->addObject($my_entity, 'xxx', $request);
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addStory($my_entity, Request $request){
    return $this->addObject($my_entity, 'yyy', $request);
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addFamily($my_entity, Request $request){
    return $this->addObject($my_entity, 'zzz', $request);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return void
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function orderMyEntityFamiliesByName($my_entity, Request $request){
    $storage = $this->entityTypeManager()->getStorage(MainHelper::FAMILY);
    $objectEntities = $storage->loadByProperties(['owner__target_id' => $my_entity]);

    $this->viewService->orderEntitiesByName($objectEntities, 'list_order_by_account');

    $destination = $request->query->get('destination');
    $this->viewService->redirectToRoute($destination);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addCollection($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
  
    /** @var \Drupal\object\Entity\product\Product $entity */
    $entity = $this->entityTypeManager()->getStorage('collection')->create();
    // Set values for the form
    $entity->set('relation', [
      'target_id' => $my_entity,
      'target_type' => MainHelper::MY_ENTITY
    ]);
  
    // build form
    $form = $this->entityFormBuilder()->getForm($entity, 'add');
    // disable relation  from the form
    $form[MainHelper::FORM_RIGHT_COLUMN][MainHelper::FORM_FIELDSET_BELONGS_TO]['relation']['widget'][0]['target_id']['#attributes']['disabled'] = 'disabled';
    $form[MainHelper::FORM_RIGHT_COLUMN][MainHelper::FORM_FIELDSET_BELONGS_TO]['relation']['widget'][0]['target_type']['#attributes']['disabled'] = 'disabled';
    return $form;
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return void
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Exception
   */
  public function orderMyEntityCollectionsByName($my_entity, Request $request){
    $storage = $this->entityTypeManager()->getStorage(MainHelper::OBJECT_COLLECTION);
    $objectEntities = $storage->loadByProperties(['relation__target_id' => $my_entity]);

    $this->viewService->orderEntitiesByName($objectEntities, 'list_order_by_account');

    $destination = $request->query->get('destination');
    $this->viewService->redirectToRoute($destination);
  }


  /**
   * @param $id
   * @param $entityType
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addObject($id, $entityType, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $id);

    /** @var \Drupal\object\Entity\product\Product $entity */
    $entity = $this->entityTypeManager()->getStorage($entityType)->create();
    // Set values for the form
    $entity->set('owner_account', [
      'target_id' => $id,
      'target_type' => MainHelper::MY_ENTITY
    ]);

    // build form
    $form = $this->entityFormBuilder()->getForm($entity, 'add');
    // hide relation and relation fields from the form
    $form[MainHelper::FORM_RIGHT_COLUMN][MainHelper::FORM_FIELDSET_BELONGS_TO]['owner_account']['widget'][0]['target_id']['#attributes']['disabled'] = 'disabled';
    $form[MainHelper::FORM_RIGHT_COLUMN][MainHelper::FORM_FIELDSET_BELONGS_TO]['owner_account']['widget'][0]['target_type']['#attributes']['disabled'] = 'disabled';
  
  
    return $form;
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Exception
   */
  public function addRelatedProduct($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getRelatedEntityView($my_entity, MainHelper::MY_ENTITY, MainHelper::PRODUCT, $request);
  }


  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addAddress($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    /** @var \Drupal\address\Entity\Address $entity */
    $entity = $this->entityTypeManager()->getStorage(MainHelper::ACCOUNT_ADDRESS)->create();
    // Set values for the form
    $entity->set('relation', [
      'target_id' => $my_entity,
      'target_type' => MainHelper::MY_ENTITY
    ]);

    // build form
    $form = $this->entityFormBuilder()->getForm($entity, 'add');

    return $form;
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function pdfListView($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountPdfListView($my_entity, MainHelper::MY_ENTITY);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addPdf($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountAddPdfForm($my_entity, MainHelper::MY_ENTITY);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function videoListView($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountVideoListView($my_entity, MainHelper::MY_ENTITY);
  }

  /**
   * @param $my_entity
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function addVideo($my_entity, Request $request) {
    $this->viewService->setDefaultPageTitle($request, MainHelper::MY_ENTITY, $my_entity);
    return $this->viewService->getAccountAddVideoForm($my_entity, MainHelper::MY_ENTITY);
  }

  /**
   * Displays a MyEntity  revision.
   *
   * @param int $my_entity_revision
   *   The MyEntity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($my_entity_revision) {
    $my_entity = $this->entityManager()->getStorage(MainHelper::MY_ENTITY)->loadRevision($my_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder(MainHelper::MY_ENTITY);

    return $view_builder->view($my_entity);
  }

  /**
   * Page title callback for a MyEntity  revision.
   *
   * @param int $my_entity_revision
   *   The MyEntity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($my_entity_revision) {
    $my_entity = $this->entityManager()->getStorage(MainHelper::MY_ENTITY)->loadRevision($my_entity_revision);
    return $this->t('Revision of %title from %date', array('%title' => $my_entity->label(), '%date' => format_date($my_entity->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a MyEntity .
   *
   * @param \Drupal\account\Entity\my_entity\MyEntityInterface $my_entity
   *   A MyEntity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(MyEntityInterface $my_entity) {
    $account = $this->currentUser();
    $langcode = $my_entity->language()->getId();
    $langname = $my_entity->language()->getName();
    $languages = $my_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $my_entity_storage = $this->entityManager()->getStorage(MainHelper::MY_ENTITY);

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $my_entity->label()]) : $this->t('Revisions for %title', ['%title' => $my_entity->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all my_entity revisions") || $account->hasPermission('administer my_entity entities')));
    $delete_permission = (($account->hasPermission("delete all my_entity revisions") || $account->hasPermission('administer my_entity entities')));

    $rows = array();

    $vids = $my_entity_storage->revisionIds($my_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\account\my_entity\MyEntityInterface $revision */
      $revision = $my_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $my_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.my_entity.revision', [MainHelper::MY_ENTITY => $my_entity->id(), 'my_entity_revision' => $vid]));
        }
        else {
          $link = $my_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.my_entity.revision_revert_translation', [MainHelper::MY_ENTITY => $my_entity->id(), 'my_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.my_entity.revision_revert', [MainHelper::MY_ENTITY => $my_entity->id(), 'my_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.my_entity.revision_delete', [MainHelper::MY_ENTITY => $my_entity->id(), 'my_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['my_entity_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    return $build;
  }

}
