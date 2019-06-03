<?php

namespace Drupal\account\Entity\my_entity;

use Drupal\account\Entity\AccountBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

/**
 * Entity type MyEntity. It extends the AccountBase class
 *
 * @package Drupal\account\Entity\my_entity
 * @category Entity
 * @author R. Hasan <hasan@company.com>
 * @since 31.01.2017
 */

/**
 * Defines the MyEntity entity.
 *
 * @ingroup account
 *
 * @ContentEntityType(
 *   id = "my_entity",
 *   label = @Translation("MyEntity"),
 *   handlers = {
 *     "storage" = "Drupal\account\my_entity\MyEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\account\my_entity\MyEntityListBuilder",
 *     "views_data" = "Drupal\account\Entity\my_entity\MyEntityViewsData",
 *     "translation" = "Drupal\account\my_entity\MyEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\account\Form\my_entity\MyEntityForm",
 *       "add" = "Drupal\account\Form\my_entity\MyEntityForm",
 *       "edit" = "Drupal\account\Form\my_entity\MyEntityForm",
 *       "navigation" = "Drupal\account\Form\my_entity\MyEntityForm",
 *       "delete" = "Drupal\account\Form\my_entity\MyEntityDeleteForm",
 *       "filters" = "Drupal\account\Form\my_entity\MyEntityForm",
 *     },
 *     "access" = "Drupal\account\my_entity\MyEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\account\my_entity\MyEntityHtmlRouteProvider",
 *       "html_diff" = "Drupal\diff\Routing\DiffRouteProvider",
 *     },
 *   },
 *   base_table = "my_entity",
 *   data_table = "my_entity_field_data",
 *   revision_table = "my_entity_revision",
 *   revision_data_table = "my_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer my_entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/my_entity/{my_entity}",
 *     "add-form" = "/admin/structure/my_entity/add",
 *     "edit-form" = "/admin/structure/my_entity/{my_entity}/edit",
 *     "delete-form" = "/admin/structure/my_entity/{my_entity}/delete",
 *     "version-history" = "/admin/structure/my_entity/{my_entity}/revisions",
 *     "revision" = "/admin/structure/my_entity/{my_entity}/revisions/{my_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/my_entity/{my_entity}/revisions/{my_entity_revision}/revert",
 *     "translation_revert" = "/admin/structure/my_entity/{my_entity}/revisions/{my_entity_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/structure/my_entity/{my_entity}/revisions/{my_entity_revision}/delete",
 *     "collection" = "/admin/structure/my_entity",
 *     "revisions-diff" = "/admin/structure/my_entity/{my_entity}/revisions/view/{left_revision}/{right_revision}/{filter}",
 *   },
 *   field_ui_base_route = "my_entity.settings"
 * )
 */
class MyEntity extends AccountBase implements MyEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['type_v3'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('V3 type'))
      ->setRevisionable(true)
      ->setDefaultValue('my_entity')
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0
      ])
      ->setSetting('allowed_values', ['my_entity' => 'MyEntity'])
      ->setDisplayConfigurable('form', true);

    $fields['logo'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Logo'))
      ->setDescription(t('Logo: 1600x600 | Quality 100%'))
      ->setRevisionable(true)
      ->setSettings([
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['image']
        ],
        'file_directory' => 'images_hres',
        'file_extensions' => 'png'
      ])
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', [
        'type' => 'entity_browser_entity_reference',
        'weight' => 0,
        'settings' => [
          'entity_browser' => 'image_browser',
          'field_widget_remove' => false,
          'open' => true,
        ]
      ])
      ->setDisplayConfigurable('form', true);
    
    return $fields;
  }

}