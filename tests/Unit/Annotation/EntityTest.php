<?php

namespace swichers\Acsf\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Entity;

/**
 * Class EntityTest
 *
 * @coversDefaultClass \swichers\Acsf\Client\Annotation\Entity
 */
class EntityTest extends TestCase {

  /**
   * @covers ::getName
   */
  public function testGetName() {

    $action = new Entity();
    $action->name = 'UnitTest';
    $this->assertEquals($action->name, $action->getName());
  }
}
