<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\Entity\EntityBase;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class EntityBaseTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Entity
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\EntityBase
 */
class EntityBaseTest extends TestCase {

  use AcsfClientTrait;

  /**
   * @covers ::id
   * @covers ::__construct
   */
  public function testId() {

    $entity = new class($this->getMockAcsfClient(), 123) extends EntityBase {

    };
    $this->assertEquals(123, $entity->id());
  }

  /**
   * @covers ::getParent
   * @covers ::__construct
   */
  public function testGetParent() {

    $parent = new class($this->getMockAcsfClient(), 123) extends EntityBase {

    };

    $entity = new class($this->getMockAcsfClient(), 456, $parent) extends EntityBase {

    };

    $this->assertEmpty($parent->getParent());
    $this->assertEquals($parent, $entity->getParent());
    $this->assertInstanceOf(EntityInterface::class, $entity->getParent());
  }

}
