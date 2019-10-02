<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Action;

/**
 * Tests for the Action annotation.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Annotation\Action
 */
class ActionTest extends TestCase {

  /**
   * Validate we can get the Action name.
   *
   * @covers ::getName
   */
  public function testGetName() {

    $action = new Action();
    $action->name = 'UnitTest';
    $this->assertEquals($action->name, $action->getName());
  }

  /**
   * Validate we can get the type of entity this Action is responsible for.
   *
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Action();
    $this->assertNull($action->getEntityType());
    $action->entityType = 'UnitTest';
    $this->assertEquals($action->entityType, $action->getEntityType());
  }

}
