<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Update;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Class UpdateTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Update
 */
class UpdateTest extends ActionTestBase {

  /**
   * @covers ::updateCode
   */
  public function testUpdateCode() {

    $action = new Update($this->getMockAcsfClient());
    $options = [
      'random' => TRUE,
      'stack_id' => -1,
      'scope' => 'sites',
      'db_update_arguments' => 'abc123',
      'sites_type' => 'registry',
      'factory_type' => 'db',
    ];
    $result = $action->updateCode('tag/unit-test', $options);
    $this->assertEquals('update', $result['internal_method']);
    $this->assertArrayNotHasKey('random', $result['json']);
    $this->assertEquals('tag/unit-test', $result['json']['sites_ref']);
    $this->assertEquals(1, $result['json']['stack_id']);
    $this->assertEquals('sites', $result['json']['scope']);
    $this->assertEquals('abc123', $result['json']['db_update_arguments']);
    $this->assertEquals('registry', $result['json']['sites_type']);
    $this->assertEquals('db', $result['json']['factory_type']);

    $result = $action->updateCode('tag/unit-test');
    $this->assertEquals(1, $result['json']['stack_id']);
    $result = $action->updateCode('tag/unit-test', ['stack_id' => 3]);
    $this->assertEquals(3, $result['json']['stack_id']);
  }

  /**
   * @covers ::updateCode
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testUpdateCodeFailDbUpdate() {

    $action = new Update($this->getMockAcsfClient());
    $action->updateCode('tag/unit-test', ['db_update_arguments' => '$']);
  }

  /**
   * @covers ::updateCode
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testUpdateCodeFailScope() {

    $action = new Update($this->getMockAcsfClient());
    $action->updateCode('tag/unit-test', ['scope' => 'abc123']);
  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Update($this->getMockAcsfClient());
    $this->assertEquals('Update', $action->getEntityType());
  }

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Update($this->getMockAcsfClient());
    $result = $action->list(['random' => TRUE, 'limit' => -1]);
    $this->assertEquals('update', $result['internal_method']);
    $this->assertEquals([], $result['query']);

  }
}
