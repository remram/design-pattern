<?php

namespace Drupal\account\Entity;

use Drupal\views\EntityViewsData;


/**
 * This is the base class for all account views data classes.
 *
 * @package Drupal\account\Entity
 * @category Entity
 * @author R. Hasan <hasan@company.com>
 * @since 07.02.2017
 */
class AccountBaseViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;   //return $this->fixGetViewsData($data);
  }

  /**
   * This is a temporary fix method for drupal. We will remove this method as
   * soon Drupal has solving it.
   * @see: https://www.drupal.org/node/2337515
   *
   * @param $data array of configuration
   * @return array
   */
  public function fixGetViewsData($data) {

    $entityType = $this->entityType->id();

    $field_definitions = $this->entityManager->getBaseFieldDefinitions($entityType);

    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_definition */
    foreach ($field_definitions as $field_definition) {

      $fieldName = $field_definition->getName();

      $type = $field_definition->getType();

      if($field_definition->getType() === 'entity_reference' &&
        $field_definition->getCardinality() === -1) {

        $data[$entityType . '__' . $fieldName][$fieldName]['relationship']['relationship field'] = $fieldName . '_target_id';

      } elseif($field_definition->getType() === 'dynamic_entity_reference' &&
        $field_definition->getCardinality() === -1) {

        $data[$entityType . '__' . $fieldName][$fieldName . '_target_id'] = $data[$entityType . '__' . $fieldName][$fieldName . '__target_id'];
        unset($data[$entityType . '__' . $fieldName][$fieldName . '__target_id']);

        $data[$entityType . '__' . $fieldName][$fieldName . '_target_type'] = $data[$entityType . '__' . $fieldName][$fieldName . '__target_type'];
        unset($data[$entityType . '__' . $fieldName][$fieldName . '__target_type']);

      } elseif($field_definition->getType() === 'list_string' &&
        $field_definition->getCardinality() === -1) {
        //bug fixing for set fields. As soon you use a set field in the exposed filter, you will have an sql error.
        //drupal is trying to access the field xxxs_set instead of xxxs_set_value

        //we are overriding wrong settings in:
        //HandlerBase::init(), line 121
        //ViewExecutable::build()
        //StringFilter::query()

        $data[$entityType . '__' . $fieldName][$fieldName]['real field'] = $fieldName . '_value';
      }
    }

    return $data;

  }

}