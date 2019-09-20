<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Groups;

/**
 * Class GroupsTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Groups
 */
class GroupsTest extends ActionTestBase {


  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $groups = new Groups($this->mockClient);
    $this->assertEquals('Group', $groups->getEntityType());
  }

  /**
   * @covers ::create
   */
  public function testCreate() {

    $groups = new Groups($this->getMockAcsfClient());
    $result = $groups->create('Unit/Test', ['parent_id' => 123, 'random_stuff' => TRUE]);
    $this->assertEquals('Unit/Test', $result['group_name']);
    $this->assertEquals('groups', $result['internal_method']);
    $this->assertEquals(123, $result['parent_id']);
    $this->assertArrayNotHasKey('random_stuff', $result);
  }

  /**
   * @covers ::list
   */
  public function testList() {

    $groups = new Groups($this->getMockAcsfClient());
    $this->assertSharedListValidation('groups', $groups, 'list');
  }



}
