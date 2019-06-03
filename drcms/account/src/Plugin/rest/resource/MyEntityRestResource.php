<?php

namespace Drupal\account\Plugin\rest\resource;

use Drupal\general\Plugin\rest\resource\CustomMediaRestResourceTrait;
use Drupal\general\Plugin\rest\resource\CustomPatchRestResourceTrait;
use Drupal\general\Plugin\rest\resource\CustomPostRestResourceTrait;
use Drupal\general\Plugin\rest\resource\CustomResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * This is a custom REST resource for my_entity. It provides the following
 * methods:
 *
 * - PATCH
 *
 * You are able to run update to my_entity and to it's relations/references by
 * calling the PATCH method in your REST call.
 *
 * A REST call can look like:
 * - URL: https://your-domain.net/api/v1/my_entity/1000006?_format=json
 * - JSON body as:
 * @code
 * {
 *   "name_en": [{
 *     "value": "Otto - EN - REST - PATCH - 005"
 *   }],
 *   "name_de": [{
 *     "value": "Otto - DE2 - REST - PATCH - 005"
 *   }],
 *   "name_it": [],  //to delete translation send empty array []
 *   "name_fr": [{
 *     "value": "Otto - FR - REST - PATCH - 005"
 *   }],
 *   ...
 * }
 * @endcode
 *
 * @package Drupal\object\Plugin\rest
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 12.07.2017
 */


/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "my_entity_rest_resource",
 *   label = @Translation("MyEntity custom rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/my_entity/{id}",
 *     "https://www.drupal.org/link-relations/create" = "/api/v1/my_entitys"
 *   }
 * )
 */
class MyEntityRestResource extends CustomResourceBase {

  use AccountRestResourceTrait;
  use MyEntityRestResourceTrait;
  use CustomPostRestResourceTrait;
  use CustomPatchRestResourceTrait;
  use CustomMediaRestResourceTrait;

  /**
   * @var \Drupal\Core\Entity\ContentEntityInterface $entity
   */
  protected $entity;


  protected function init() {
    // Merge config attributes
    $this->allowedAttributes = array_merge($this->allowedAttributes, $this->customAllowedAttributes);
    $this->relationalAttributes = array_merge($this->relationalAttributes, $this->customRelationalAttributes);
    $this->translatableAttributes = array_merge($this->translatableAttributes, $this->customTranslatableAttributes);
    $this->reverseRelation = array_merge($this->reverseRelation, $this->customReverseRelation);

    $this->availableAliases = $this->getAliases($this->allowedAttributes);
    // Remove ID
    unset($this->availableAliases['id']);

    // make sure isOldMediaFile flag is not set in CMS.
    $this->fileService->deleteIsOldMediaFileSetting();
  }

  /**
   * Responds to PATCH requests.
   *
   * Return the updated entity
   *
   * @param string $id of the entity
   * @param array $data the JSON body as array
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\rest\ResourceResponse
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function patch($id, $data, Request $request) {
    $this->init();

    $results = $this->updateEntity('my_entity', $id, $data);

    // Return whole entity as array to resource response
    return new ResourceResponse($results);
  }


  public function post($data, Request $request) {
    $this->init();

    $results = $this->postData('my_entity', $data);

    // Return whole entity as array to resource response
    return new ResourceResponse($results);
  }


}
