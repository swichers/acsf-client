<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\EntityBase;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the EntityBase abstract class.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\EntityBase
 *
 * @group AcsfClient
 */
class EntityBaseTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can get the Id of the entity.
   *
   * @covers ::id
   * @covers ::__construct
   */
  public function testId() {

    $entity = new class($this->getMockAcsfClient(), 123) extends EntityBase {

    };
    $this->assertEquals(123, $entity->id());
  }

  /**
   * Validate we can get an entity parent.
   *
   * @covers ::getParent
   * @covers ::__construct
   */
  public function testGetParent() {

    $parent = new class($this->getMockAcsfClient(), 123) extends EntityBase {

    };

    $entity = new class($this->getMockAcsfClient(
    ), 456, $parent) extends EntityBase {

    };

    $this->assertEmpty($parent->getParent());
    $this->assertEquals($parent, $entity->getParent());
    $this->assertInstanceOf(EntityInterface::class, $entity->getParent());
  }

}
