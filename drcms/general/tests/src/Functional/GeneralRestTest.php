<?php

namespace Drupal\Tests\general\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\general\Helper\MainHelper;

/**
 * Class GeneralRestTest
 *
 * We are using the product entity for the test of general queries. We have to use
 * our own endpoint. This is why the usage of product entity.
 *
 * Here we will test for example some general filter like count=true or
 * count_by_field=image etc.
 *
 * @package Drupal\Tests\general\Functional
 * @author R. Hasan <hasan@company.com>
 * @since 08.06.2018
 */
class GeneralRestTest extends GeneralResourceTestBase {

  private static $id;
  private static $postJson = '{"items":[{"code_2":[{"value":"SK"}],"code_3":[{"value":"SOK"}],"continent_code_2":[{"value":"AS"}],"continent_code_3":[{"value":"Asien"}],"order":[{"value":1}],"name_en":[{"value":"South Korea"}],"name_de":[{"value":"Südkorea"}]}]}';
  private static $resourceNamePlural = 'countries';

  protected static $resource;

  public function testRest() {
    static::$entityType = MainHelper::COUNTRY;

    $testMethods = preg_grep('/^runArcTest_/', get_class_methods($this));

    foreach ($testMethods as $method) {
      $this->{$method}();
    }
  }

  public function runArcTest_Post() {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->postAssertion(static::$resourceNamePlural, static::$postJson);
    static::$id = (int) $dataArray[0]['id'][0]['value'];
  }

  public function runArcTest_Get_Test_Count() {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAssertion(static::$resourceNamePlural, ['count' => 'true']);
    $this->assertTrue($dataArray['meta']['count']);
  }

  public function runArcTest_Get_Test_CountByField() {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAssertion(static::$resourceNamePlural, ['count_by_field' => 'id']);
    $this->assertTrue($dataArray['meta']['count_by_field']);
  }

  public function runArcTest_Get_Test_Changed() {
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAssertion(static::$resourceNamePlural, ['changed' => 864000000]);

    $this->assertArrayHasKey('items', $dataArray);
    $this->assertArrayHasKey(0, $dataArray['items']);
    $this->assertArrayHasKey('id', $dataArray['items'][0]);
    $this->assertArrayHasKey(0, $dataArray['items'][0]['id']);
    $this->assertArrayHasKey('value', $dataArray['items'][0]['id'][0]);
  }
}