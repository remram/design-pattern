<?php

namespace Drupal\general\Plugin\rest\resource;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigException;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Site\Settings;
use Drupal\file\FileInterface;
use Drupal\general\Helper\MainHelper;
use Drupal\image\Plugin\Field\FieldType\ImageItem;
use Drupal\media\MediaInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;


/**
 * Trait CustomGetRestResourceTrait
 *
 * A trait for all custom GET REST resources. It provides a common place for all
 * important methods for our custom REST endpoints.
 *
 * @package Drupal\general\Plugin\rest\resource
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 04.07.2017
 */
trait CustomGetRestResourceTrait {

  protected static $objectGroupResult = [];
  protected static $filterTypeResult = [];

  /**
   * It will create a query depending to the parameters and filters and will
   * query the target entity. And it will return an array of results.   *
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param array $attributes list of fields
   * @param string $entityType
   *    The name of the entity type e.g.: product or collection etc.
   * @param string $orderBy
   *    Use the alias of attribute(field)
   * @param string $direction
   *    Use either ASC or DESC
   * @param string $offset
   *    Number to start from
   * @param string $limit
   *    The number of the result
   *
   * @return array
   */
  protected function getListByParams($request, $attributes, $entityType,
                                     $orderBy, $direction,
                                     $offset, $limit) {

    $offset = intval($offset);
    $limit = intval($limit);
    $direction = ($direction === 'desc') ? 'DESC' : 'ASC';

    $langCode = $this->getLangCode($orderBy);
    $orderBy = array_key_exists($orderBy, $this->availableAliases) ? $this->availableAliases[$orderBy] : 'id';

    $this->checkAccess();

    if ($offset < 0) {
      throw new BadRequestHttpException('Not allowed offset received. Number must be > or = 0');
    }

    if ($limit < 0) {
      throw new BadRequestHttpException('Not allowed limit received. Number must be > or = 0');
    } elseif($limit === 0) {
      $limit = static::DEFAULT_LIMIT;
    }

    // Normalize the attributes
    list($attributes, $translatableAttributes, $relationalAttributes) = $this->normalizeAttributes($attributes);

    /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
    $query = $this->getFilteredQuery($entityType, $request);

    /*...*/

    $query->sort($orderBy, $direction, $langCode)->range($offset, $limit);
    $ids = $query->execute();

    // Query all entities by the ID list
    $entities = $this->entityTypeManager->getStorage($entityType)->loadMultiple($ids);

    // Process the results
    $items = $this->getResults($entities, $attributes, $translatableAttributes, $relationalAttributes);
    $result = ['meta' => $meta,'items' => $items];

    return $result;
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param array $attributes list of fields
   * @param array $data the JSON body as array
   * @param string $entityType
   *    The name of the entity type e.g.: product or collection etc.
   * @param string $idAttr
   *
   * @return array
   */
  protected function getListByIds(Request $request, $attributes, $data, $entityType, $idAttr='id') {

    $this->checkAccess();

    $queryRequest = $request->query->all();

    /*...*/

    // Normalize the attributes
    list($attributes, $translatableAttributes, $relationalAttributes) = $this->normalizeAttributes($attributes);

    /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
    $query = $this->getFilteredQuery($entityType, $request);

    /*...*/

    $query->sort($params['order_by'], $params['direction']);
    $ids = $query->execute();

    // Query all entities by the ID list
    $entities = $this->entityTypeManager->getStorage($entityType)->loadMultiple($ids);

    // Process the results
    $items = $this->getResults($entities, $attributes, $translatableAttributes, $relationalAttributes);

    return ['meta' => [], 'items' => $items];
  }


  /**
   * It takes the $request and creates a query object (QueryInterface) and checks
   * if the current $request has a query string. If there are values (filter) in
   * the query string, it will add filter to the query object (QueryInterface)
   *
   * @param string $entityType
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   */
  protected function getFilteredQuery($entityType, Request $request) {

    // Request query as array
    $requestQuery = $request->query->all();

    $queryConjunction = (isset($requestQuery['query_conjunction']) && strtoupper(trim($requestQuery['query_conjunction'])) === 'OR') ? 'OR' : 'AND';
    $specialFilter = isset($requestQuery['special_filter']) ? Json::decode(base64_decode(urldecode(trim($requestQuery['special_filter'])))) : NULL;

    /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
    $query = $this->entityTypeManager
      ->getStorage($entityType)
      ->getQuery($queryConjunction);

    // Standard conjunction is AND for where statement
    $group = $query->andConditionGroup();
    if($queryConjunction === 'OR') {
      $group = $query->orConditionGroup();
    }

    foreach ($requestQuery as $alias => $queryValue) {

      if($attribute = $this->getAttributeByAlias($alias)) {
        // Convert values form comma separated to array: a,b => ['a', 'b']
        $requestValues = explode(',', $queryValue);

        /*...*/
      }
    }

    // If special filter is provided
    if($specialFilter) {
      if(count($group->conditions()) > 0) {
        $query->condition($group);
        $group = $query->andConditionGroup();
      }

      foreach ($specialFilter as $alias => $filterRecord) {
        if($attribute = $this->getAttributeByAlias($alias)) {
          // Add language code to the condition if the field is translatable
          $langCode = NULL;
          if(in_array($attribute, $this->translatableAttributes)) {
            $langCode = $this->getLangCode($alias);
          }

          /*...*/
          $group->condition($filter['field'], $filter['value'], htmlspecialchars_decode($filterRecord['op'], ENT_NOQUOTES), 'en');
        }

      }
    }

    // If changed since filter is provided
    if($changedSince = $request->get('changed_since')) {
      if(count($group->conditions()) > 0) {
        $query->condition($group);
        $group = $query->andConditionGroup();
      }

      $group->condition('changed', trim($changedSince), '>=', 'en');
    }

    // If changed till is provided
    $changedTill = $request->get('changed_till');
    if( ($changedTill && $changedSince && ($changedSince < $changedTill)) || ($changedTill && !$changedSince) ) {
      if(count($group->conditions()) > 0) {
        $query->condition($group);
        $group = $query->andConditionGroup();
      }

      $group->condition('changed', trim($changedTill), '<=', 'en');
    }

    if(count($group->conditions()) > 0) {
      $query->condition($group);
    }

    return $query;
  }

  protected function setFilter($entityType, $attribute, $alias, $requestValue) {

    // Add language code to the condition if the field is translatable
    $langCode = NULL;
    if(in_array($attribute, $this->translatableAttributes)) {
      $langCode = $this->getLangCode($alias);
    }

    $operator = isset($this->allowedAttributes[$attribute][$alias]) ?
      $this->allowedAttributes[$attribute][$alias] : NULL;

    // Relational fields which defined as dynamic_entity_reference
    // require additional information regarding filter!
    // You should put in your Trait class configuration information about
    // the name of your filed name.
    // For example you have a field called relation then you should
    // pust something like this in the config:
    //
    // 'relation' => [
    //     'get' => 'getAccountRelation',
    //     'set' => 'setAccountRelation',
    //     'filter' => [
    //         'field_name' => 'relation__target_id'
    //     ],
    // ],
    //
    // The real field name would be => relation__target_id
    if(isset($this->relationalAttributes[$attribute]['filter']['field_name'])) {
      $attribute = $this->relationalAttributes[$attribute]['filter']['field_name'];
    }

    if(isset($this->relationalAttributes[$attribute]['filter'])) {
      $method = $this->relationalAttributes[$attribute]['filter'];
      // Call the proper method depending to the relational entity
      // As example please take a look to methods: filterByAccountRelation()
      // filterByObjectRelation() or filterCollection()
      /*...*/
    } else {
      /*...*/
    }

    return $params;
  }

  /**
   * This will create a filter configuration for galleries s.a. product, family
   * or antique.
   *
   * @param string $entityType
   * @param string $bundle
   * @param string $imageFieldName
   * @param string $filterValue
   * @return array|null
   */


  /**
   * IT goes through the data array and returns a list of IDs
   *
   * @param array $data
   *    A list of data, which contains the body of post data
   *
   * @return array of id
   */

  /**
   * It returns a list of result of all entities. It queries regular
   * attributes, translatable attributes and relational attributes.
   *
   * @param array $entities
   *    A list of entities of \Drupal\Core\Entity\ContentEntityInterface
   * @param array $attributes
   *    A list of attributes
   * @param $translatableAttributes
   *    A list of translatable attributes
   * @param $relationalAttributes
   *    A list of relational attributes
   *
   * @return array or results
   */
  protected function getResults($entities, $attributes, $translatableAttributes, $relationalAttributes) {
    $result = [];
    $counter = 0;
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    foreach ($entities as $entity) {

      // Collect all attributes without translation and relation
      foreach ($attributes as $attribute) {
        $result[$counter][array_search($attribute, $this->availableAliases)] =
          $entity->{$attribute};
      }

      // Collect all translations for all translatable attributes
      foreach ($translatableAttributes as $alias => $attribute) {
        // Take the language code from the suffix of the alias name
        $langCode = $this->getLangCode($alias);

        if ($entity->hasTranslation($langCode)) {
          // Get the translation
          $translation = $entity->getTranslation($langCode);
          $result[$counter][$alias] = $translation->{$attribute};
        } else {
          // Save null for no translation
          $result[$counter][$alias] = [];
        }
      }

      foreach ($relationalAttributes as $alias => $attribute) {
        // Collect all relational attributes
        $relationResults = $this->getRelation(
          $this->entityTypeManager,
          $entity,
          $entity->id(),
          $alias,
          $attribute);

        if($relationResults) {
          $result[$counter] = array_merge($result[$counter], $relationResults);
        }
      }

      // Special fields. They will be available anyway
      $result[$counter]['changed'] = $entity->changed;
      $result[$counter]['changed_human_internal'] = [
        [ 'value' => date('Y-m-d H:i:s', $entity->get('changed')->value) ],
      ];

      $counter++;
    }

    return $result;
  }


  /**
   * It returns a list of all relational attributes of external entity, such
   * as object collection.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param $id
   * @param $alias
   * @param $attribute
   *
   * @return array
   */
  protected function getRelation(EntityTypeManagerInterface $entityTypeManager,
                                 ContentEntityInterface $entity, $id, $alias,
                                 $attribute) {
    $result = [];

    if(count($this->relationalAttributes) > 0 && array_key_exists($attribute,
        $this->relationalAttributes) &&
      array_key_exists('get', $this->relationalAttributes[$attribute])) {

      $method = $this->relationalAttributes[$attribute]['get'];
      // Call the proper method depending to the relational entity
      $result = $this->{$method}($entityTypeManager, $entity, $id, $attribute, $alias);
    }

    return (count($result) > 0) ? $result : false;
  }


  /**
   * It returns a list of entities of reversed relation.
   *
   * For example, we have two entities:
   * - Product
   * - Collection
   *
   * $entity parameter is a Product entity but product has no relation to
   * collection. Only collection knows about the relation to product.
   * Therefore this function will query collection by given product entity
   * and will return a list of collection entities.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param $attribute
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */

  /**
   * It will query external entity by given the id of the current entity and
   * will return configured values as an array plus the id of the result array
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param $id
   * @param $attribute
   * @param $alias
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */


  /**
   * It queries the reverse relation and will return a config array for the
   * filter
   *
   * @param array $config
   * @param string $attribute
   * @param integer $id
   *
   * @return array|null
   */

  /**
   * It grabs all the chain of parent ids by given initial id and will return an
   * array of all founded ids.
   *
   * for example:
   * - you give $id = 3242903
   * - you will receive an array as:
   *
   * @code
   * [
   *    0 => [
   *       "id": "30001",
   *       "name": "Product"
   *    ],
   *    1 => [
   *       "id": "3210006",
   *       "name": "Sanitaryware"
   *    ],
   *    2 => [
   *       "id": "3220894",
   *       "name": "Bathrooms taps"
   *    ],
   *    3 => [
   *       "id": "3232922",
   *       "name": "Wash-basin taps"
   *    ],
   *    4 => [
   *       "id": "3242903",
   *       "name": "deck mounted / countertop washbasin taps"
   *    ]
   * ]
   * @endcode
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param $id
   * @param $parentField
   * @param bool $defaultInfo
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function getHierarchy(EntityTypeManagerInterface $entityTypeManager,
                                  ContentEntityInterface $entity, $id, $parentField, $defaultInfo = false) {

    $result = [];
    $entityType = $entity->getEntityTypeId();

    /** @var ContentEntityInterface $entity */
    $entity = $entityTypeManager->getStorage($entityType)->load($id);

    if($entity) {
      // If parent ID exists, call the method recursive.
      if($entity->get($parentField)->target_id) {
        $result = $this->getHierarchy($entityTypeManager, $entity, $entity->get($parentField)->target_id, $parentField, $defaultInfo);
      }

      if($defaultInfo) {
        $result[] = [
          'target_id' => $entity->id(),
          'target_type' => $entityType,
          'name' => $entity->get('name')->value,
        ];
      } else {
        $result[] = [
          'id' => $entity->id(),
          'name' => $entity->get('name')->value,
        ];
      }
    }

    return $result;
  }


  /**
   * It normalize the given attribute and returns an array of arrays.
   *
   * @param $attributes
   *
   * @return array
   */


  /**
   * It returns the image path
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   * @param string $attribute
   * @param string $alias
   * @param string $fileField
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */

  /**
   * @param $entityType
   * @param $attribute
   * @param $alias
   *
   * @return array
   */

  /**
   * It returns the image path
   *
   * @param string $entityType
   * @param MediaInterface $mediaEntity
   * @param string $imageFieldName
   * @param string $attribute
   * @param string $alias
   *
   * @return array
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */

  /**
   * It returns the image path
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $mediaEntity
   * @param string $attribute
   * @param string $alias
   * @param string $fileField
   * @param string $folderName
   *
   * @return array
   */

  /**
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param $id
   * @param $attribute
   * @param $alias
   * @param bool $loadMultiple
   *
   * @return array
   */

  /**
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param $objectGroupRelation
   * @param int $level
   * @param $alias
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */

  /**
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param string $entityType
   * @param EntityReferenceFieldItemListInterface $filterRelation
   * @param string $filterType
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
}