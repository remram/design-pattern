<?php

namespace Drupal\general\Plugin\rest\resource;


use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\arc_media\Service\FileService;
use Drupal\arc_media\Service\MediaService;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CustomResourceBase
 *
 * @package Drupal\general\Plugin\rest\resource *
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 13.07.2017
 */
class CustomResourceBase extends ResourceBase {

  use CustomRestResourceTrait;

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * A current entity type manager interface
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * @var array of available aliases and there configuration
   */
  protected $availableAliases;

  /**
   * @var FileService
   */
  protected $fileService;

  /**
   * @var MediaService
   */
  protected $mediaService;
  
  
  /**
   * @var array of data of one row
   */
  protected $dataRow;

  /**
   * @var array of configuration of translation
   */
  protected $translationConfig = [];

  /**
   * @var array of error thrown by set method in each trait of custom entity
   */
  protected $relationErrors = [];

  /**
   * @var array of successful inserted IDs
   */
  protected $insertedIdList = [];

  /**
   * @var int indicate the record number during insert
   */
  protected $recordNumber = 1;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   A current entity type manager interface
   * @param FileService $fileService
   * @param MediaService $mediaService
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
    FileService $fileService,
    MediaService $mediaService) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileService = $fileService;
    $this->mediaService = $mediaService;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('arc.media.file_service'),
      $container->get('arc.media.media_service')
    );
  }
}