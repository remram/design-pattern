<?php

namespace Drupal\Tests\account\Unit;


use Drupal\general\Helper\MainHelper;
use Drupal\Tests\general\Unit\GeneralUnitTestCase;

/**
 * Class MyEntityRestAttributeTest
 *
 * @package Drupal\Tests\account\Unit
 * @author R. Hasan <hasan@company.com>
 * @since 08.06.2018
 */
class MyEntityRestAttributeTest extends GeneralUnitTestCase {

  public function setUp() {
    parent::setUp();

    $this->entity = MainHelper::ACCOUNT;
    $this->endPointPrefix = ucfirst(MainHelper::MY_ENTITY);
  }

  public function testRestAttributes() {
    $this->executeTestRestAttributes();
  }

  public function testRestListAttributes() {
    $this->executeTestRestListAttributes();
  }

}