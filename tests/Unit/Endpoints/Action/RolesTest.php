<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Roles;

/**
 * Class RolesTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Roles
 */
class RolesTest extends ActionTestBase {

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Roles($this->getMockAcsfClient());
    $this->assertSharedListValidation('roles', $action, 'list');
  }

  /**
   * @covers ::create
   */
  public function testCreate() {

    $action = new Roles($this->getMockAcsfClient());
    $result = $action->create('UnitTest');
    $this->assertEquals('roles', $result['internal_method']);
    $this->assertEquals('UnitTest', $result['json']['name']);

  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Roles($this->mockClient);
    $this->assertSame('Role', $action->getEntityType());
  }

}
