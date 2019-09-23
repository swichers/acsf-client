<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Role;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class RoleTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Entity
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Role
 */
class RoleTest extends TestCase {

  use AcsfClientTrait;

  /**
   * @covers ::details
   */
  public function testDetails() {

    $entity = new Role($this->getMockAcsfClient(), 1234);
    $result = $entity->details();
    $this->assertEquals('roles/1234', $result['internal_method']);
  }

  /**
   * @covers ::update
   */
  public function testUpdate() {

    $entity = new Role($this->getMockAcsfClient(), 1234);
    $result = $entity->update('UnitTest');
    $this->assertEquals('roles/1234', $result['internal_method']);
    $this->assertEquals('UnitTest', $result['json']['new_name']);
  }

  /**
   * @covers ::delete
   */
  public function testDelete() {

    $entity = new Role($this->getMockAcsfClient(), 1234);
    $result = $entity->delete();
    $this->assertEquals('roles/1234', $result['internal_method']);
  }

}
