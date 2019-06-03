<?php

namespace Drupal\account\Plugin\rest\resource;


use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\general\Helper\MainHelper;
use Drupal\media\MediaInterface;

trait MyEntityRestResourceTrait {

  protected $customAllowedAttributes = [
    'type_v3' => ['type_v3' => '='],
    'logo' => [
      'logo' => 'LIKE',
      'logo_internal' => 'LIKE'
    ],
  ];

  protected $customRelationalAttributes = [
    'logo' => [
      'get' => 'getImage',
      'set' => 'setImage',
    ],
  ];

  protected $customTranslatableAttributes = [];

  protected $customReverseRelation = [];


  /**
   * It returns the image path
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param integer $id
   * @param string $attribute
   * @param string $alias
   *
   * @return array
   */
  protected function getImage(EntityTypeManagerInterface $entityTypeManager,
                              ContentEntityInterface $entity, $id, $attribute,  $alias) {

    $image = $this->queryImage($entity, $attribute, $alias, 'field_image');

    return $image;
  }

  /**
   * It sets/saves the image to the current entity
   *
   * @param $attribute
   * @param $alias
   * @param $value
   *
   * @return mixed
   */
  protected function setImage($attribute, $alias, $value) {
    if (empty($value)) {
      // remove image reference
      $this->removeValue($attribute);
      return;
    }

    $mediaFieldMap = Settings::get('media.fields.map');

    return $this->saveImage(
      MainHelper::MY_ENTITY,
      $mediaFieldMap[MainHelper::MY_ENTITY][$attribute],
      $value[0]['value'],
      [
        'bundle' => 'image', //image or image
        'file_field_name' => 'field_image', //field_image or field_image
        'attribute' => $attribute, //image_1, image_2, etc.
      ]);
  }
}