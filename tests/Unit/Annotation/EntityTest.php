<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Entity;

/**
 * Tests for the Entity annotation.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Annotation\Entity
 *
 * @group AcsfClient
 */
class EntityTest extends TestCase {

  /**
   * Validate we can get the Annotation name.
   *
   * @covers ::getName
   */
  public function testGetName() {

    $action = new Entity();
    $action->name = 'UnitTest';
    $this->assertEquals($action->name, $action->getName());
  }

}
