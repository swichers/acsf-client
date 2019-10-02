<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Roles;

/**
 * Tests for the RolesTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Roles
 *
 * @group AcsfClient
 */
class RolesTest extends AbstractActionTestBase {

  /**
   * Validate we can list Roles.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Roles($this->getMockAcsfClient());
    $this->assertSharedListValidation('roles', $action, 'list');
  }

  /**
   * Validate we can create Roles.
   *
   * @covers ::create
   */
  public function testCreate() {

    $action = new Roles($this->getMockAcsfClient());
    $result = $action->create('UnitTest');
    $this->assertEquals('roles', $result['internal_method']);
    $this->assertEquals('UnitTest', $result['json']['name']);

  }

  /**
   * Validate we get the Role entity type.
   *
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Roles($this->mockClient);
    $this->assertSame('Role', $action->getEntityType());
  }

}
