<?php

namespace Drupal\account\Plugin\rest\resource;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\general\Helper\MainHelper;

trait AccountRestResourceTrait {

  protected $allowedAttributes = [
    'id' => ['id' => '='],
    'name' => ['name' => 'LIKE'],
    'old_id' => ['old_id' => '='],
    'access_status' => ['access' => '='],
    'xxxs_set' => ['xxxs' => '='],
    'header' => [
      'header_en' => 'LIKE',
      'header_de' => 'LIKE',
      'header_it' => 'LIKE',
      'header_es' => 'LIKE',
      'header_fr' => 'LIKE',
      'header_nl' => 'LIKE',
      'header_sv' => 'LIKE',
      'header_da' => 'LIKE',
      'header_nb' => 'LIKE',
    ],
  ];

  protected $relationalAttributes = [
    'filter_relation' => [
      'get' => 'getAllFilter',
      'set' => 'setAllFilter',
    ],
  ];

  protected $translatableAttributes = [
    'header',
  ];

  protected $reverseRelation = [];


  /**
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param $id
   * @param $attribute
   * @param $alias
   *
   * @return mixed
   */
  protected function getAllFilter(EntityTypeManagerInterface $entityTypeManager,
                                         ContentEntityInterface $entity, $id,
                                         $attribute,  $alias) {
    return [$alias => $entity->{$attribute}];
  }

  /**
   * @param string $attribute
   * @param string $alias
   * @param array $value
   */
  protected function setAllFilter($attribute, $alias, $value) {
    $this->updateRelation('filter', $attribute, $alias, $value);
  }

}