<?php

namespace Drupal\account\my_entity;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the MyEntity entity.
 *
 * @see \Drupal\account\Entity\MyEntity.
 */
class MyEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\account\Entity\MyEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished my_entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published my_entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit my_entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete my_entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add my_entity entities');
  }

}
