<?php

namespace Drupal\general\Plugin\rest\resource;

use Drupal\Component\Serialization\Json;

/**
 * Trait CustomPostRestResourceTrait
 *
 * A trait for all custom GET REST resources. It provides a common place for all
 * important methods for our custom REST endpoints.
 *
 * @package Drupal\general\Plugin\rest\resource
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 07.07.2017
 */
trait CustomPostRestResourceTrait {


  /**
   * This is the main method for posting/saving new data to the targeted entity.
   *
   * @param string $entityType
   * @param array $data
   *
   * @return array of results
   */
  protected function postData($entityType, $data) {

    //check $data if it contains the key 'items'
    $hasItems = $this->checkItems($data);
    if(is_array($hasItems)) {
      die(Json::encode($hasItems));
    }

    $this->checkAccess();

    // ID is a special case, therefore we need to add it manually
    $this->availableAliases['id'] = 'id';

    $results = [];
    foreach ($data['items'] as $key => $dataRow) {
      $this->dataRow = $dataRow;
      // Create empty entity
      if($this->entity = $this->entityTypeManager->getStorage($entityType)->create()) {
        // Check the JSON attributes
        $this->checkAttributes($dataRow);

        // remove all empty attributes from the array
        $this->removeEmptyAttributes($dataRow);

        //set the attributes
        $this->setAttributes($dataRow);

        try {
          //validate the inputs
          $this->saveEntity($this->entity, ['validate' => TRUE, 'setInsertedId' => TRUE, 'finalCall' => TRUE]);

          $results[] = $this->entity->toArray();
        } catch (\Exception $e) {

          $error = [
            'record' => $this->recordNumber,
            'message' => $e->getMessage()
          ];

          die(Json::encode($error));
        }

        $this->recordNumber++;
      }
    }

    return $results;
  }

  /**
   * @param $data
   *
   * @return array|bool
   */
  protected function checkItems(array $data) {
    if(!array_key_exists('items', $data)) {
      return [
        'record' => 'Initial key: (items)',
        'message' => 'The key (items) is missing. Please check your POST body!'
      ];
    }

    return true;
  }
}