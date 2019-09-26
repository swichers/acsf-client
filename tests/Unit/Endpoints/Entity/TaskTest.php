<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Task;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class TaskTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Entity
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Task
 */
class TaskTest extends TestCase {

  use AcsfClientTrait;

  /**
   * @covers ::status
   */
  public function testStatus() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->status();
    $this->assertEquals('wip/task/1234/status', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * @covers ::stop
   */
  public function testStop() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->stop();
    $this->assertEquals('tasks/1234', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * @covers ::delete
   */
  public function testDelete() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->delete();
    $this->assertEquals('tasks/1234', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * @covers ::logs
   */
  public function testLogs() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $options = [
      'random' => TRUE,
      'descendants' => 'yes',
      'level' => 'debug',
    ];
    $result = $action->logs($options);
    $this->assertEquals('tasks/1234/logs', $result['internal_method']);
    $this->assertEquals('debug', $result['query']['level']);
    $this->assertTrue($result['query']['descendants']);
    $this->assertArrayNotHasKey('random', $result['query']);

    $this->assertEquals('emergency', $action->logs(['level' => 'emergency'])['query']['level']);
    $this->assertEquals('alert', $action->logs(['level' => 'alert'])['query']['level']);
    $this->assertEquals('critical', $action->logs(['level' => 'critical'])['query']['level']);
    $this->assertEquals('error', $action->logs(['level' => 'error'])['query']['level']);
    $this->assertEquals('warning', $action->logs(['level' => 'warning'])['query']['level']);
    $this->assertEquals('notice', $action->logs(['level' => 'notice'])['query']['level']);
    $this->assertEquals('info', $action->logs(['level' => 'info'])['query']['level']);
    $this->assertEquals('debug', $action->logs(['level' => 'debug'])['query']['level']);
  }

  /**
   * @covers ::logs
   * @depends testLogs
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testLogsFailLevel() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $action->logs(['level' => 'abc123']);
  }

  /**
   * @covers ::pause
   */
  public function testPause() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->pause(TRUE, ['random' => TRUE, 'level' => 'task']);
    $this->assertEquals('pause/1234', $result['internal_method']);
    $this->assertTrue($result['json']['paused']);
    $this->assertArrayNotHasKey('random', $result['json']);

    $result = $action->pause(FALSE);
    $this->assertFalse($result['json']['paused']);

    $result = $action->pause(TRUE, ['level' => 'task']);
    $this->assertEquals('task', $result['json']['level']);
    $result = $action->pause(TRUE, ['level' => 'family']);
    $this->assertEquals('family', $result['json']['level']);
  }

  /**
   * @covers ::pause
   * @depends testPause
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testPauseFailLevel() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $action->pause(TRUE, ['level' => 'abc123']);
  }

  /**
   * @covers ::resume
   *
   * @depends testPause
   * @depends testPauseFailLevel
   */
  public function testResume() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->resume(['random' => TRUE, 'level' => 'family']);
    $this->assertEquals('pause/1234', $result['internal_method']);
    $this->assertFalse($result['json']['paused']);
    $this->assertArrayNotHasKey('random', $result['json']);
  }

}
