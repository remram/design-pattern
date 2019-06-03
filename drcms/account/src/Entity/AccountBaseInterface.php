<?php

namespace Drupal\account\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * This is the base interface for the AccountBase class.
 *
 * @package Drupal\account\Entity
 * @category Entity
 * @author R. Hasan <hasan@company.com>
 * @since 31.01.2017
 */
interface AccountBaseInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the MyEntity name.
   *
   * @return string
   *   Name of the MyEntity.
   */
  public function getName();

  /**
   * Sets the MyEntity name.
   *
   * @param string $name
   *   The MyEntity name.
   *
   * @return \Drupal\account\Entity\my_entity\MyEntityInterface
   *   The called MyEntity entity.
   */
  public function setName($name);

  /**
   * Gets the MyEntity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the MyEntity.
   */
  public function getCreatedTime();

  /**
   * Sets the MyEntity creation timestamp.
   *
   * @param int $timestamp
   *   The MyEntity creation timestamp.
   *
   * @return \Drupal\account\Entity\my_entity\MyEntityInterface
   *   The called MyEntity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the MyEntity published status indicator.
   *
   * Unpublished MyEntity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the MyEntity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a MyEntity.
   *
   * @param bool $published
   *   TRUE to set this MyEntity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\account\Entity\my_entity\MyEntityInterface
   *   The called MyEntity entity.
   */
  public function setPublished($published);

  /**
   * Gets the MyEntity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the MyEntity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\account\Entity\my_entity\MyEntityInterface
   *   The called MyEntity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the MyEntity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the MyEntity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\account\Entity\my_entity\MyEntityInterface
   *   The called MyEntity entity.
   */
  public function setRevisionUserId($uid);

}