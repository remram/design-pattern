<?php

namespace Drupal\Tests\account\Functional;

use Drupal\general\Helper\MainHelper;
use Drupal\Tests\general\Functional\GeneralResourceTestBase;

/**
 * Class MyEntityRestTest
 *
 * @package Drupal\Tests\account\Functional
 * @author R. Hasan <hasan@company.com>
 * @since 17.07.2018
 */
class MyEntityRestTest extends GeneralResourceTestBase {

  private static $id = 1;
  private static $resourceNameSingular = MainHelper::MY_ENTITY;
  private static $resourceNamePlural = 'my_entitys';

  public function testRest() {
    static::$entityType = static::$resourceNameSingular;
    $this->runAllArcTests($this);
  }


  /**
   * #############    Test POST    ################
   */
  public function runArcTest_Post() {
    $json = $this->getScenarioFile(static::$resourceNamePlural. '.post.json');
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->postAssertion(static::$resourceNamePlural, $json);

    $this->arcAssertArrayHasKey($dataArray, [0, 'id', 0, 'value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
    //static::$id = (int) $dataArray[0]['id'][0]['value'];
  }


  /**
   * #############    Test PATCH    ################
   */
  public function runArcTest_Patch() {
    $json = $this->getScenarioFile(static::$resourceNamePlural. '.patch.json');
    $this->patchAssertion(static::$resourceNameSingular, $json, static::$id, 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
  }

  public function runArcTest_Patch_Access() {
    $json = '{"access": [{ "value": "offline" }]}';
    $this->patchAssertion(static::$resourceNameSingular, $json, static::$id, 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
  }


  /**
   * #############    Test GET    ################
   */
  public function runArcTest_GetAsPost() {
    $queryJson = str_replace('@id', static::$id, static::$getAsPost);

    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAsPostAssertion(static::$resourceNamePlural, $queryJson, static::$id);

    $this->arcAssertArrayHasKey($dataArray, ['items', 0, 'id', 0, 'value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
  }

  public function runArcTest_Get() {
    $this->getAssertion(static::$resourceNamePlural);
  }


  /**
   * #############    Test Filters    ################
   */
  public function runArcTest_Get_ByFilter_ID() {
    $expected = static::$id;
    $key = 'id';
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAssertion(static::$resourceNamePlural, [$key => $expected]);

    $this->arcAssertArrayHasKey($dataArray, ['items', 0, $key, 0, 'value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
    $this->assertEquals($expected, $dataArray['items'][0][$key][0]['value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
  }

  public function runArcTest_Get_ByFilter_Name() {
    $expected = 'PHPUnit (scenario-patching): Vitra';
    $key = 'name';
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAssertion(static::$resourceNamePlural, [$key => $expected]);

    $this->arcAssertArrayHasKey($dataArray, ['items', 0, $key, 0, 'value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
    $this->assertEquals($expected, $dataArray['items'][0][$key][0]['value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
  }

  public function runArcTest_Get_ByFilter_access() {
    $expected = 'online';
    $key = 'access';
    /** @var \Psr\Http\Message\ResponseInterface $response */
    /** @var string $dataJson */
    /** @var array $dataArray */
    list($response, $dataJson, $dataArray) = $this->getAssertion(static::$resourceNamePlural, [$key => $expected]);

    $this->arcAssertArrayHasKey($dataArray, ['items', 0, $key, 0, 'value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
    $this->assertEquals($expected, $dataArray['items'][0][$key][0]['value'], 'Error @ ' . __LINE__ . ' in: ' . __METHOD__);
  }
}