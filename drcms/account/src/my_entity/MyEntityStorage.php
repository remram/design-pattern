<?php

namespace Drupal\account\my_entity;

use Drupal\account\AccountStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\account\Entity\my_entity\MyEntityInterface;

/**
 * Defines the storage handler class for MyEntity entities.
 *
 * This extends the base storage class, adding required special handling for
 * MyEntity entities.
 *
 * @ingroup account
 */
class MyEntityStorage extends AccountStorage implements MyEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(MyEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {my_entity_revision} WHERE id=:id ORDER BY vid',
      array(':id' => $entity->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {my_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      array(':uid' => $account->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(MyEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {my_entity_field_revision} WHERE id = :id AND default_langcode = 1', array(':id' => $entity->id()))
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('my_entity_revision')
      ->fields(array('langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED))
      ->condition('langcode', $language->getId())
      ->execute();
  }

}