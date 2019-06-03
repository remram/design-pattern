<?php

namespace Drupal\Tests\general\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Class GeneralUnitTestCase
 *
 * @package Drupal\Tests\general\Unit
 * @author R. Hasan <hasan@company.com>
 * @since 08.06.2018
 */
abstract class GeneralUnitTestCase extends UnitTestCase {

  protected $entity;
  protected $endPointPrefix;

  public function executeTestRestAttributes() {
    $className = "Drupal\\{$this->entity}\\Plugin\\rest\\resource\\{$this->endPointPrefix}RestResource";
    $this->assertClassHasAttribute('entity', $className);
    $this->assertClassHasAttribute('allowedAttributes', $className);
    $this->assertClassHasAttribute('relationalAttributes', $className);
    $this->assertClassHasAttribute('translatableAttributes', $className);
    $this->assertClassHasAttribute('reverseRelation', $className);
  }

  public function executeTestRestListAttributes() {
    $className = "Drupal\\{$this->entity}\\Plugin\\rest\\resource\\{$this->endPointPrefix}ListRestResource";
    $this->assertClassHasAttribute('filter', $className);
    $this->assertClassHasAttribute('allowedAttributes', $className);
    $this->assertClassHasAttribute('relationalAttributes', $className);
    $this->assertClassHasAttribute('translatableAttributes', $className);
    $this->assertClassHasAttribute('reverseRelation', $className);
  }
}