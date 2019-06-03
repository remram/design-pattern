<?php

namespace Drupal\general\Plugin\rest\resource;

use Drupal\Core\Config\ConfigException;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\general\Helper\ArcEntityHelper;


/**
 * Trait CustomPatchRestResourceTrait
 *
 * A trait for all custom PATCH REST resources. It provides a common place for
 * all important methods for our custom REST endpoints.
 *
 * @package Drupal\general\Plugin\rest\resource
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 04.07.2017
 */
trait CustomPatchRestResourceTrait {

  protected $structure = [
    'regular' => [
      'value',
    ],
    'relation' => [
      'target_id',
      'target_type',
    ],
    'relation_optional' => [
      'target_id',
    ],
  ];

  /**
   * It checks the value of the given structure. It returns true in case if
   * value does exist and false if there is no value.
   *
   * @param array $attributeStructure
   *
   * @return bool true|false
   */
  protected function hasValue($attributeStructure) {
    return (isset($attributeStructure[0]['value']) && $attributeStructure[0]['value']) ? true : false;
  }


  /**
   * It checks the value of the given structure. It returns true in case if
   * value does exist and false if there is no value.
   *
   * @param array $attributeStructure
   *
   * @return bool true|false
   */
  protected function hasTargetValue($attributeStructure) {
    return (isset($attributeStructure[0]['target_id']) && $attributeStructure[0]['target_id']) ? true : false;
  }


  /**
   * It checks all attributes given through JSON. It compare the names of
   * attributes with existing one in the database.
   *
   * @throws \Drupal\Core\Config\ConfigException
   *
   * @param $data
   */
  protected function checkAttributes($data) {

    $fieldDefinition = $this->entity->getFieldDefinitions();

    foreach ($this->availableAliases as $alias => $attribute) {
      // Check the attribute if it is a part of the entity attribute list OR
      // a part of the $relationalAttributes list AND it exists in the
      // $data
      if((array_key_exists($attribute, $fieldDefinition) ||
          array_key_exists($attribute, $this->relationalAttributes)) &&
        array_key_exists($alias, $data)) {

        // Remove attribute from $data
        unset($data[$alias]);
      }
    }

    // If $data still containing data, then throw exception
    if(count($data) > 0) {
      $dataAttributes = array_keys($data);
      $attributes = implode(', ', $dataAttributes);
      throw new ConfigException("Your JSON contains not allowed attributes: [{$attributes}]. Please check your JSON structure!");
    }
  }


  /**
   * It checks the input array if it contains any empty values. If an attribute
   * is empty, it will remove that one from the request array.
   *
   * ATTENTION: Call this method only during POST (insert) but never during
   *            PATCH (update)! Otherwise you will be not able to remove existing
   *            data from the database.
   *
   * @param $data
   */
  protected function removeEmptyAttributes(&$data) {

    $dataArr = $data;

    foreach ($dataArr as $attribute => $value) {
      if(!$this->hasValue($value) && !$this->hasTargetValue($value)) {
        unset($data[$attribute]);
      }
    }
  }


  /**
   * It checks/validate the attributes key/structure to avoid errors during
   * the update process. If user sends wrong structure, he/she will run to an
   * exception error.
   *
   * @param string $type it is as validation type
   * @param array $attributeStructure the structure of the current attribute
   * @param string $attributeName
   */
  protected function checkStructure($type, $attributeStructure, $attributeName) {

    if(array_key_exists($type, $this->structure)) {
      // Get the rule for the current type
      $rule = $this->structure[$type];

      $count = 0;
      $allowedKeys = implode(', ', $rule);

      // If array is empty -> throw exception error
      if(count($attributeStructure) >= 1) {
        foreach ($attributeStructure as $attribute) {
          foreach ($attribute as $attKey => $attValue) {
            if(!in_array($attKey, $rule)) {
              throw new ConfigException("Allowed keys are [{$allowedKeys}]
            Given [{$attKey}] in [{$attributeName}] with index [{$count}]'!");
            }
          }
          $count++;
        }
      }
    }
  }

  /**
   * It reorders the user input in proper way. We use the "$this->allowedAttributes"
   * array a look up order.
   *
   * @param array $data unordered user input
   *
   * @return array of ordered user input
   */
  protected function reorderData($data) {
    $orderedData = [];

    foreach ($this->allowedAttributes as $attribute => $value) {
      foreach ($value as $key => $operator) {
        if(array_key_exists($key, $data)) {
          $orderedData[$key] = $data[$key];
        }
      }
    }

    return $orderedData;
  }

  /**
   * Sets all regular attribute to the entity.
   *
   * @param array $data of input data. It comes from JSON input
   */
  protected function setAttributes($data) {

    // Reorder user input
    $data = $this->reorderData($data);

    // Update revision and changed fields of the entity
    $this->entity = ArcEntityHelper::setNewRevision($this->entity);

    // Add first only translatable and entity attributes
    foreach ($data as $alias => $value) {
      if($attribute = $this->getAttributeByAlias($alias)) {

        if($alias === $attribute . '_internal') {
          unset($data[$alias]);
          continue;
        } elseif(array_key_exists($attribute, $this->relationalAttributes)) {
          continue;
        }

        if(in_array($attribute, $this->translatableAttributes)) {
          $this->setTranslation($this->entity, $attribute, $alias, $value);
          unset($data[$alias]);
        } else {
          $this->checkStructure('regular', $value, $alias);

          // Update certain attribute
          $this->entity->set($attribute, $value);
          unset($data[$alias]);
        }
      }
    }

    //set the attribute changed to the current entity record
    $this->entity->set('changed', time());

    $this->entity = $this->commitTranslationConfig($this->entity);

    // Now add relational attributes
    foreach ($data as $alias => $value) {
      if($attribute = $this->getAttributeByAlias($alias)) {
        if(array_key_exists($attribute, $this->relationalAttributes)) {
          $this->uniqueValueList($value);
          $this->setRelation($attribute, $alias, $value);
          unset($data[$alias]);
        }
      }
    }
  }

  /**
   * @param $entityType
   * @param $id
   * @param $data
   *
   * @return mixed
   */
  protected function updateEntity($entityType, $id, $data) {

    $this->checkAccess();

    $this->entity = $this->entityTypeManager->getStorage($entityType)->load($id);

    // Check the JSON attributes
    $this->checkAttributes($data);

    $this->setAttributes($data);

    // Validate the entity and save it
    $this->saveEntity($this->entity, ['validate' => TRUE, 'setInsertedId' => FALSE, 'finalCall' => TRUE]);

    $this->recordNumber++;

    return $this->entity->toArray();
  }

  /**
   * Sets all translatable attribute to the entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param string $attribute current attribute name
   * @param string $alias current alias name of the attribute
   * @param array $value current value
   */
  protected function setTranslation(ContentEntityInterface $entity, $attribute, $alias, $value) {
    $langCode = $this->getLangCode($alias);
    $this->checkStructure('regular', $value, $alias);
    $defaultLanguage = $entity->language()->getId();

    if($langCode === $defaultLanguage) {
      // Set the value in traditional way
      $entity->set($attribute, $value);
    } else {
      $this->translationConfig[$langCode][$attribute] = isset($value[0]) ? $value[0] : null;
    }
  }

  /**
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   */
  protected function commitTranslationConfig(ContentEntityInterface $entity) {

    foreach ($this->translationConfig as $langCode => $attributes) {
      $this->saveTranslation($entity, $langCode, $attributes);
    }

    //reset the translation config
    $this->translationConfig = [];

    return $entity;
  }

  /**
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param string $langCode such as 'de'
   * @param array $attributes such as ['name' (attribute name) => 'Translated name' (value)]
   */
  protected function saveTranslation(ContentEntityInterface &$entity, $langCode, $attributes) {

    // Add the translation
    if (!$entity->hasTranslation($langCode)) {
      $entity->addTranslation($langCode, $attributes);
    } else {
      // Update an existing translation
      foreach ($attributes as $attribute => $value) {
        $entity->getTranslation($langCode)
          ->set($attribute, $value);
      }
    }
  }


  /**
   * It checks the attribute, if it's a part of the main entity or not.
   * Is it a part of main entity -> it will call setRelations()
   * If not -> it will call updateEntityRelation()
   *
   * @param string $attribute current attribute name
   * @param string $alias current alias name of the attribute
   * @param array $value current value
   */
  protected function setRelation($attribute, $alias, $value) {
    if(isset($this->relationalAttributes[$attribute]['set']) && $method = $this->relationalAttributes[$attribute]['set']) {
      /*...*/
    }
    return;
  }

  /**
   * Sets all relational attributes to the entity.
   *
   * @param $targetType
   * @param $attribute
   * @param $alias
   * @param $value
   * @param $validated
   */
  protected function updateRelation($targetType, $attribute, $alias, $value, $validated = FALSE, $forceSet = FALSE) {
    if(!$validated) {
      $this->checkStructure('relation_optional', $value, $alias);
    }

    $validValue = [];

    foreach ($value as $key => $val) {
      // Set the target type if it's given otherwise use the default value $targetType!
      $targetType = isset($val['target_type']) ? $val['target_type'] : $targetType;
      //check if the entity exists
      if($this->hasEntity($targetType, $val['target_id']) || $forceSet) {
        $validValue[$key] = $val;
        $validValue[$key] += [
          'target_type' => $targetType
        ];
      } else {
        $this->relationErrors[] = "There are no entities matching {$val['target_id']} in {$targetType}.";
      }
    }

    // Update entity only when either has valid values or the value array is empty
    if(count($validValue) > 0 || count($value) === 0) {
      // Update certain attribute
      /*...*/
    }

  }

  /**
   * it checks the entity whether is available or not.
   *
   * @param string $entityType
   * @param string $id
   *
   * @return bool
   */

  /**
   * This is a special method for User entity. It will insert/update the password
   * by using the custom REST API endpoint for user.
   *
   * @param array $value
   */

  /**
   * It checks the id of the field the same as the id form the entity. If they are
   * the same, we execute save() otherwise we ignore it.
   *
   * @param $value
   */

  /**
   * It update all relations of an entity. The relation does not exist in the
   * main entity. Therefore main entity has no idea where all these relation
   * exist.
   *
   * For example take a look at the object collection. This entity can have
   * different relation to different object types (product, xxx, antique,
   * etc.) Therefor we need to update all relations/references in each object
   * collection entity.
   *
   * Object collection = relational entity
   *
   * @param string $attribute current attribute name
   * @param string $alias current alias name of the attribute
   * @param array $value current value
   *
   * @return void
   */

  /**
   * It adds a new relation/reference in the relational entity of main entity.
   *
   * @param string $entityType
   * @param string $relationName
   * @param string $targetType
   * @param array $data the JSON body as array
   */

  /**
   * It deletes all the relations/references in certain relational entity.
   *
   * @param array $entities list of relational entities
   * @param $relationName
   * @param array $data the JSON body as array
   */

  /**
   * @param $attribute
   * @param $alias
   * @param $value
   * @param $relationConfig
   *
   * @return string
   */


  /**
   * This method was invented to handle the requirement for object and account filter.
   * Filter entities for object and account are holding all types of filters:
   *
   * Object filter:
   * - obj_1st_letter
   * - obj_country
   * - ...
   * - obj_theme
   * - obj_use
   *
   * Account filter:
   * - country_1 ... 3
   * - year
   * - employee
   * - ...
   *
   * For example a product has a relation to object filter and using obj_theme
   * and obj_use. Normally in save/update/delete relation we do update all IDs.
   *
   * Here we have different use case! We have to separate the IDs by type. The
   * client of the REST API is able to send only the updated list of obj_theme!
   *
   * In this case we only update the ID list which are related to obj_theme and
   * we do not touch the ID list of obj_use!
   *
   *
   * @param string $entityType e.g: filter
   * @param string $attribute e.g: type
   * @param string $fieldValue e.g: obj_use or obj_theme or country_2 etc.
   * @param string $lookupAttribute e.g: filter_relation
   * @param array $givenIdList e.g: see the code section below
   *
   * <code>
   * [
   *    ['target_id' => 100],
   *    ['target_id' => 999],
   *    ['target_id' => 153]
   * ]
   * </code>
   *
   * @return array e.g: see the code section below
   *
   * <code>
   * [
   *    ['target_id' => 100],
   *    ['target_id' => 654],
   *    ['target_id' => 855]
   *    ['target_id' => 999],
   *    ['target_id' => 153]
   * ]
   * </code>
   */


  /**
   * It will cleans up the list of multiple time of the same value.
   *
   * @param array $values
   */
}