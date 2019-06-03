<?php
/**
 * company CMS
 *
 * @link http://www.company.com/
 * @copyright Copyright (c) 2017 company AG
 */

namespace Drupal\account;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\general\Service\SequencesService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * Defines the base storage handler class for Account entities.
 *
 * It makes sure that all account entity types are using same sequence table to get
 * next auto-increment value for entity id.
 *
 * @package Drupal\account
 * @category Storage
 * @author hasan <hasan@company.com>
 * @since 31.01.17
 */
class AccountStorage extends SqlContentEntityStorage {

  /**
   * Flag to indicate if the cleanup function in __destruct() should run.
   * @var bool
   */
  protected $needsCleanup = FALSE;

  /**
   * @var SequencesService
   */
  protected $sequencesService;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('database'),
      $container->get('entity.manager'),
      $container->get('cache.entity'),
      $container->get('language_manager'),
      $container->get('arc.general.sequences_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    Connection $database,
    EntityManagerInterface $entity_manager,
    CacheBackendInterface $cache,
    LanguageManagerInterface $language_manager,
    SequencesService $sequencesService) {

    parent::__construct($entity_type, $database, $entity_manager, $cache, $language_manager);

    $this->sequencesService = $sequencesService;
  }

  /**
   * {@inheritdoc}
   */
  public function __destruct() {
    if ($this->needsCleanup) {
      $this->nextIdDelete();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function doSaveFieldItems(ContentEntityInterface $entity, array $names = []) {
    $entity = $this->sequencesService->doSaveFieldItems($entity, $names, 'account');
    return parent::doSaveFieldItems($entity, $names);
  }

  public function nextId($existing_id = 0) {
    return $this->sequencesService->nextId($existing_id);
  }

  public function nextIdDelete() {
    $this->sequencesService->nextIdDelete();
  }
}