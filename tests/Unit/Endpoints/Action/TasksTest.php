<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Tasks;

/**
 * Class TasksTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Tasks
 */
class TasksTest extends ActionTestBase {

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Tasks($this->getMockAcsfClient());
    $this->assertSharedListValidation('tasks', $action, 'list');

    $options = [
      'group' => 'anything',
      'status' => 'processing',
      'class' => 'softpaused',
    ];
    $result = $action->list($options);
    $this->assertEquals('processing', $result['query']['status']);
    $this->assertEquals('anything', $result['query']['group']);
    $this->assertEquals('softpaused', $result['query']['class']);

    $options = [
      'status' => 'error',
      'class' => 'softpause-for-update',
    ];
    $result = $action->list($options);
    $this->assertEquals('error', $result['query']['status']);
    $this->assertEquals('softpause-for-update', $result['query']['class']);


    $result = $action->list(['status' => 'not-started']);
    $this->assertEquals('not-started', $result['query']['status']);

  }

  /**
   * @covers ::list
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testListFailStatus() {

    $action = new Tasks($this->getMockAcsfClient());
    $action->list(['status' => 'abc123']);
  }

  /**
   * @covers ::list
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testListFailClass() {

    $action = new Tasks($this->getMockAcsfClient());
    $action->list(['class' => 'abc123']);
  }

  /**
   * @covers ::pause
   */
  public function testPause() {

    $action = new Tasks($this->getMockAcsfClient());

    $options = [
      'reason' => 'this is the reason',
      'random' => TRUE,
    ];

    $result = $action->pause(TRUE, $options);
    $this->assertEquals('pause', $result['internal_method']);
    $this->assertTrue($result['json']['paused']);
    $this->assertEquals('this is the reason', $result['json']['reason']);
    $this->assertArrayNotHasKey('random', $result['json']);

    $result = $action->pause(FALSE);
    $this->assertFalse($result['json']['paused']);
  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Tasks($this->getMockAcsfClient());
    $this->assertSame('Task', $action->getEntityType());
  }

  /**
   * @covers ::getClassInfo
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testGetClassInfo() {

    $action = new Tasks($this->getMockAcsfClient());
    $result = $action->getClassInfo('softpaused');
    $this->assertEquals('classes/softpaused', $result['internal_method']);
    $result = $action->getClassInfo('softpause-for-update');
    $this->assertEquals('classes/softpause-for-update', $result['internal_method']);

    $action->getClassInfo('abc123');
  }

}
