<?php

namespace Drupal\account\my_entity;

use Drupal\account\AccountStorageInterface;
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
interface MyEntityStorageInterface extends AccountStorageInterface {

  /**
   * Gets a list of MyEntity revision IDs for a specific MyEntity.
   *
   * @param \Drupal\account\Entity\my_entity\MyEntityInterface $entity
   *   The MyEntity entity.
   *
   * @return int[]
   *   MyEntity revision IDs (in ascending order).
   */
  public function revisionIds(MyEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as MyEntity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   MyEntity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\account\Entity\my_entity\MyEntityInterface $entity
   *   The MyEntity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(MyEntityInterface $entity);

  /**
   * Unsets the language for all MyEntity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}