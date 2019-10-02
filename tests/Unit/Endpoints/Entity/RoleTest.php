<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Role;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Role entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Role
 *
 * @group AcsfClient
 */
class RoleTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can get Role details.
   *
   * @covers ::details
   */
  public function testDetails() {

    $entity = new Role($this->getMockAcsfClient(), 1234);
    $result = $entity->details();
    $this->assertEquals('roles/1234', $result['internal_method']);
  }

  /**
   * Validate we can update a Role.
   *
   * @covers ::update
   */
  public function testUpdate() {

    $entity = new Role($this->getMockAcsfClient(), 1234);
    $result = $entity->update('UnitTest');
    $this->assertEquals('roles/1234', $result['internal_method']);
    $this->assertEquals('UnitTest', $result['json']['new_name']);
  }

  /**
   * Validate we can delete a Role.
   *
   * @covers ::delete
   */
  public function testDelete() {

    $entity = new Role($this->getMockAcsfClient(), 1234);
    $result = $entity->delete();
    $this->assertEquals('roles/1234', $result['internal_method']);
  }

}
