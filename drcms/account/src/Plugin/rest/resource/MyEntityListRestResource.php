<?php

namespace Drupal\account\Plugin\rest\resource;

use Drupal\general\Plugin\rest\resource\CustomGetRestResourceTrait;
use Drupal\general\Plugin\rest\resource\CustomResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is a custom REST resource for my_entitys. It provides the following
 * methods:
 *
 * - POST
 * - GET
 *
 * You are able to receive a list of my_entitys through this custom REST API.
 *
 * The call for POST requires:
 * - URL: https://your-domain.net/api/v1/my_entitys/null?_format=json
 *
 * The POST URL contains parameters
 * /api/v1/my_entitys/{attributes}?_format=json
 *
 * The call for GET requires:
 * - URL: https://your-domain.net/api/v1/my_entitys/2/10/id/asc/null?_format=json
 *
 * The GET URL contains parameters and filters
 * /api/v1/my_entitys/{offset}/{limit}/{orderBy}/{direction}/{attributes}?_format=json&{filters}
 *
 * - offset     -> number: It sets the query form where to start
 * - limit      -> number: It limits the number of the returned my_entitys
 * - orderBy    -> string: This must be an attribute name
 * - direction  -> string: Use either ASC or DESC
 * - attributes -> string: comma separated attribute names like "id,name_en,date" If you put "null", it will return you all attributes.
 * - filters    -> string: Separated by (&) like "...?_format=json&name_en=Egan OpenOffice&status=preview"
 *
 * Available options for POST
 * --------------------------
 * - attributes -> optional
 * - filters    -> ignored
 *
 * The POST body contains list of IDs
 *
 * @code
 * {
 *   "id": [
 *     {"value":1000006},
 *     {"value":1000007},
 *     {...}
 *   ]
 * }
 * @endcode
 *
 *
 * Available options for GET
 * -------------------------
 * - offset     -> mandatory
 * - limit      -> mandatory
 * - orderBy    -> mandatory
 * - direction  -> mandatory
 * - attributes -> optional
 * - filters    -> optional
 *
 * GET does not allow a body!
 *
 * @package Drupal\account\Plugin\rest
 * @category Resource
 * @author R. Hasan <hasan@company.com>
 * @since 12.07.2017
 */

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "my_entity_list_rest_resource",
 *   label = @Translation("MyEntity list custom rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/my_entitys/{offset}/{limit}/{order_by}/{direction}/{attributes}",
 *     "https://www.drupal.org/link-relations/create" = "/api/v1/my_entitys/{attributes}"
 *   }
 * )
 */
class MyEntityListRestResource extends CustomResourceBase {

  use CustomGetRestResourceTrait;
  use AccountRestResourceTrait;
  use MyEntityRestResourceTrait;

  const ENTITY_TYPE = 'my_entity';
  const DEFAULT_LIMIT = 10;

  /**
   * @var array of filter
   */
  protected $filter;

  protected function init() {
    // Merge config attributes
    $this->allowedAttributes = array_merge($this->allowedAttributes, $this->customAllowedAttributes);
    $this->relationalAttributes = array_merge($this->relationalAttributes, $this->customRelationalAttributes);
    $this->translatableAttributes = array_merge($this->translatableAttributes, $this->customTranslatableAttributes);
    $this->reverseRelation = array_merge($this->reverseRelation, $this->customReverseRelation);

    $this->availableAliases = $this->getAliases($this->allowedAttributes);
  }

  /**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * The body of the POST request should contains a JSON array with the value
   * of all requested IDs
   * @code
   * {
   *   "id": [
   *     {"value":1000006},
   *     {"value":1000007}
   *   ]
   * }
   * @endcode
   *
   * @param $attributes
   * @param array $data the JSON body as array
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function post($attributes, $data, Request $request) {
    $this->init();

    $result = $this->getListByIds($request, $attributes, $data, self::ENTITY_TYPE);

    $response = new ResourceResponse($result);
    $response->addCacheableDependency($result);
    return $response;
  }


  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   *
   * @param $offset
   * @param $limit
   * @param $orderBy
   * @param $direction
   * @param $attributes
   * @param $data
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($offset, $limit, $orderBy, $direction, $attributes, $data, Request $request) {
    $this->init();

    $result = $this->getListByParams($request, $attributes, self::ENTITY_TYPE,
      $orderBy, $direction,
      $offset, $limit);

    $response = new ResourceResponse($result);
    $response->addCacheableDependency($result);
    return $response;
  }

}
