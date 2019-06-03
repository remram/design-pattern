<?php

namespace Drupal\general\Plugin\rest\resource;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\ContentEntityInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


/**
 * Trait CustomRestResourceTrait
 *
 * A trait for all custom REST resources. It provides a common place for all
 * important methods for our custom REST endpoints.
 *
 * @package Drupal\general\Plugin\rest\resource
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 04.07.2017
 */
trait CustomRestResourceTrait {

  /**
   * It checks the permission for the current user
   */
  protected function checkAccess() {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
  }


  /**
   * It returns the attribute name by given the alias name. It returns false
   * if the alias not found.
   *
   * @param string $alias
   *
   * @return bool|mixed
   */
  protected function getAttributeByAlias($alias) {
    $alias = trim($alias);
    if(array_key_exists($alias, $this->availableAliases)) {
      return $this->availableAliases[$alias];
    }

    return false;
  }

  /**
   * It flips the array key <-> value and returns a flat array
   *
   * Input array ($array)
   * --------------------
   * @code
   * [
   *   'id' => [
   *     'unique_key'
   *   ],
   *   'name' => [
   *     'name_en',
   *     'name_de',
   *     'name_fr'
   *   ]
   * ]
   * @endcode
   *
   * Output array ($result)
   * ----------------------
   * @code
   * [
   *   'unique_key' => 'id',
   *   'name_en' => 'name',
   *   'name_de' => 'name',
   *   'name_fr' => 'name'
   * ]
   * @endcode
   *
   *
   * @param null|array $array input array
   * @param null $currentKey
   * @param boolean $allow
   *
   * @return array output array
   */
  protected function getAliases($array = null, $currentKey = null, $allow = true) {
    $result = [];

    if (!is_array($array)) {
      $array = func_get_args();
    }

    foreach ($array as $key => $value) {
      if ($allow && is_array($value)) {
        $result = array_merge($result, $this->getAliases($value, $key, false));
      } else {
        $key = ($key)?: $value;
        $result = array_merge($result, [$key => $currentKey] );
      }
    }

    return $result;
  }

  /**
   * Returns the language code extracted from the name of the alias.
   *
   * @param string $alias
   *
   * @return mixed
   */
  protected function getLangCode($alias) {
    $aliasArr = explode('_', trim($alias));
    return (is_array($aliasArr) && isset($aliasArr[1])) ? end($aliasArr) : 'en';
  }

  /**
   * It behaves depending on the given options. It will validate if the option
   * is given. If there no errors it will save the entity. Otherwise either it
   * end the program by outputting the error message or keeps the error messages
   * for the final call.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param array $options
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveEntity(ContentEntityInterface $entity, $options = []) {

    $options = [
      'validate' => isset($options['validate']) ? $options['validate'] : FALSE,
      'validateOnly' => isset($options['validateOnly']) ? $options['validateOnly'] : FALSE,
      'setInsertedId' => isset($options['setInsertedId']) ? $options['setInsertedId'] : FALSE,
      'finalCall' => isset($options['finalCall']) ? $options['finalCall'] : FALSE,
    ];

    $errMsg = '';

    if($options['validate'] || $options['validateOnly']) {
      // check if there are any entity validation errors.
      /** @var \Drupal\Core\Entity\EntityConstraintViolationList $errors */
      $errors = $entity->validate();
      foreach ($errors as $error) {
        $attributeName = $error->getPropertyPath();
        $errMsg .= 'Error (' . $attributeName . '): ' . $error->getMessage() . " \n ";
      }
    }

    if(empty($errMsg) && !$options['validateOnly']) {
      $entity->save();
      if(!in_array($entity->id(), $this->insertedIdList) && $options['setInsertedId']) {
        $this->insertedIdList[] = $entity->id();
      }
    }

    if(($options['finalCall'] && $options['validate']) || $options['validateOnly']) {
      // include relation errors as well, if available any.
      if(count($this->relationErrors) > 0) {
        $errMsg .= ' - custom error: ' . implode('; ', $this->relationErrors);

        $this->relationErrors = [];
      }


      if (!empty($errMsg)) {
        $error = [
          'record' => $this->recordNumber,
          'message' => $errMsg
        ];

        // by inserting a list of entities, it could happen, that one of them is
        // wrong. Therefore we throw an error message, which contains a list of
        // all successfully added entity IDs. It's a helpful information to tell
        // the user about all inserted IDs and on which row (entity) something
        // went wrong.
        if(count($this->insertedIdList) > 0) {
          $error['inserted_ids'] = $this->insertedIdList;
        }

        //print error as json
        die(Json::encode($error));
      }
    }
  }
}