<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Groups;

/**
 * Tests for the GroupsTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Groups
 *
 * @group AcsfClient
 */
class GroupsTest extends AbstractActionTestBase {

  /**
   * Validate we get a Group entity type.
   *
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Groups($this->mockClient);
    $this->assertEquals('Group', $action->getEntityType());
  }

  /**
   * Validate we can create a Group.
   *
   * @covers ::create
   */
  public function testCreate() {

    $action = new Groups($this->getMockAcsfClient());
    $result = $action->create(
      'Unit/Test',
      [
        'parent_id' => 123,
        'random_stuff' => TRUE,
      ]
    );
    $this->assertEquals('Unit/Test', $result['json']['group_name']);
    $this->assertEquals('groups', $result['internal_method']);
    $this->assertEquals(123, $result['json']['parent_id']);
    $this->assertArrayNotHasKey('random_stuff', $result['json']);
  }

  /**
   * Validate we can list Groups.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Groups($this->getMockAcsfClient());
    $this->assertSharedListValidation('groups', $action, 'list');
  }

}
