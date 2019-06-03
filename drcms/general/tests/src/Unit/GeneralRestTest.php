<?php

namespace Drupal\Tests\general\Unit;

use Drupal\Core\Url;
use Drupal\general\Helper\MainHelper;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\Client;

/**
 * Class GeneralRestTest
 *
 * @package Drupal\Tests\general\Unit
 * @author R. Hasan <hasan@company.com>
 * @since 08.06.2018
 */
class GeneralRestTest extends UnitTestCase {

  public function testAttributes() {
    $className = 'Drupal\general\Plugin\rest\resource\CustomResourceBase';
    $this->assertClassHasAttribute('currentUser', $className);
    $this->assertClassHasAttribute('entityTypeManager', $className);
    $this->assertClassHasAttribute('availableAliases', $className);
    $this->assertClassHasAttribute('fileService', $className);
    $this->assertClassHasAttribute('mediaService', $className);
    $this->assertClassHasAttribute('dataRow', $className);
    $this->assertClassHasAttribute('translationConfig', $className);
    $this->assertClassHasAttribute('relationErrors', $className);
  }

  /*public function testGetListByIds() {

    $data = [
      'id' => [
        ['value' => 8992245]
      ]
    ];

    //die(var_dump(Url::fromUserInput('/api/v1/countries/null?_format=json')));

    //$client = new Client()

    $mock = $this->getMockForTrait('Drupal\general\Plugin\rest\resource\CustomGetRestResourceTrait');

    $mock->expects($this->any())
      ->method('getListByIds')
      ->will($this->returnValue(true));

    $this->assertTrue($mock->getListByIds(null, $data, MainHelper::COUNTRY));

    // Configure the stub.
    $stub->method('getListByIds')
      ->will(
        $this->returnArgument(0),
        $this->returnArgument(1),
        $this->returnArgument(2));



    // Calling $stub->getListByIds() will now return
    $results = $stub->getListByIds(null, $data, MainHelper::COUNTRY);
    // 'foo'.
    $this->assertEquals('foo', $results);
  }*/
}