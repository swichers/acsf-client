<?php

namespace swichers\Acsf\Client\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Action;

/**
 * Class ActionTest
 *
 * @coversDefaultClass \swichers\Acsf\Client\Annotation\Action
 */
class ActionTest extends TestCase {

  /**
   * @covers ::getName
   */
  public function testGetName() {

    $action = new Action();
    $action->name = 'UnitTest';
    $this->assertEquals($action->name, $action->getName());
  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Action();
    $this->assertNull($action->getEntityType());
    $action->entity_type = 'UnitTest';
    $this->assertEquals($action->entity_type, $action->getEntityType());
  }
}
