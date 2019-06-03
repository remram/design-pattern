<?php

namespace Drupal\account\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\user\UserInterface;

/**
 * This is the base class for all account types. AccountBase will be
 * inherited by all entity types such as:
 * - my_entity
 * - ...
 *
 * @package Drupal\account\Entity
 * @category Entity
 * @author R. Hasan <hasan@company.com>
 * @since 31.01.2017
 */
class AccountBase extends RevisionableContentEntityBase implements AccountBaseInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the my_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? true : false);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionUser() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionUserId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    /**
     * Fields are defined by Drupal console
     */

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the MyEntity entity.'))
      ->setRevisionable(true)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(true)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', true)
      ->setDisplayConfigurable('view', true);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the entity.'))
      ->setRequired(true)
      ->setRevisionable(true)
      ->setSettings(array(
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', true);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the MyEntity is published.'))
      ->setRevisionable(true)
      ->setDefaultValue(true);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Revision timestamp'))
      ->setDescription(t('The time that the current revision was created.'))
      ->setQueryable(false)
      ->setRevisionable(true);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revision user ID'))
      ->setDescription(t('The user ID of the author of the current revision.'))
      ->setSetting('target_type', 'user')
      ->setQueryable(false)
      ->setRevisionable(true);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(true)
      ->setRevisionable(true)
      ->setTranslatable(true);

    /**
     * company custom fields
     */

    $fields['old_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Old ID'))
      ->setRequired(true)
      ->setRevisionable(true)
      ->setDefaultValue(0)
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', ['weight' => 0])
      ->setSettings([
        'unsigned' => true,
        'size' => 'big',
        'min' => 0,
        'max' => null
      ])
      ->setDisplayConfigurable('form', true);

    $fields['access_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Access status'))
      ->setRequired(true)
      ->setRevisionable(true)
      ->setDefaultValue('preview')
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0
      ])
      ->setSetting('allowed_values', [
        'new' => 'New',
        'preview' => 'Preview',
        'soon' => 'Soon',
        'online' => 'Online',
        'offline' => 'Offline',
        'archived' => 'Archived',
        'deleted' => 'Kill/Destroy'
      ])
      ->setDisplayConfigurable('form', true);

    $fields['online_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Online date'))
      ->setRevisionable(true)
      //->setSettings(['datetime_type' => DateTimeItem::DATETIME_TYPE_DATE])
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', [
        'type' => 'datetime',
        'weight' => 0
      ])
      ->setDisplayConfigurable('form', true);

    $fields['list_order'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('List order'))
      ->setRequired(true)
      ->setRevisionable(true)
      ->setDefaultValue(900000000000000)
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', [
        'weight' => 0
      ])
      ->setSettings([
        'unsigned' => true,
        'size' => 'big',
        'min' => 1,
        'max' => null
      ])
      ->setDisplayConfigurable('form', true);


    $fields['group_relation'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Account group'))
      ->setRevisionable(true)
      ->setSettings(['target_type' => 'group'])
      ->setCardinality(-1)
      ->setDisplayOptions('view', ['label' => 'above'])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ])
      ->setDisplayConfigurable('form', true);


    return $fields;
  }
}