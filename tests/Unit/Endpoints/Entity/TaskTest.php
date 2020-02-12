<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Endpoints\Entity\Task;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Task entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Task
 *
 * @group AcsfClient
 */
class TaskTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can get the Task status.
   *
   * @covers ::status
   */
  public function testStatus() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->status();
    $this->assertEquals('wip/task/1234/status', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * Validate we can stop a Task.
   *
   * @covers ::stop
   */
  public function testStop() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->stop();
    $this->assertEquals('tasks/1234', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * Validate we can delete a Task.
   *
   * @covers ::delete
   */
  public function testDelete() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $result = $action->delete();
    $this->assertEquals('tasks/1234', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * Validate we can get the logs for a Task.
   *
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

    $this->assertEquals(
      'emergency',
      $action->logs(['level' => 'emergency'])['query']['level']
    );
    $this->assertEquals(
      'alert',
      $action->logs(['level' => 'alert'])['query']['level']
    );
    $this->assertEquals(
      'critical',
      $action->logs(['level' => 'critical'])['query']['level']
    );
    $this->assertEquals(
      'error',
      $action->logs(['level' => 'error'])['query']['level']
    );
    $this->assertEquals(
      'warning',
      $action->logs(['level' => 'warning'])['query']['level']
    );
    $this->assertEquals(
      'notice',
      $action->logs(['level' => 'notice'])['query']['level']
    );
    $this->assertEquals(
      'info',
      $action->logs(['level' => 'info'])['query']['level']
    );
    $this->assertEquals(
      'debug',
      $action->logs(['level' => 'debug'])['query']['level']
    );
  }

  /**
   * Validate we get an exception when getting logs of an invalid level.
   *
   * @covers ::logs
   *
   * @depends testLogs
   */
  public function testLogsFailLevel() {

    $action = new Task($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $action->logs(['level' => 'abc123']);
  }

  /**
   * Validate we can pause a Task.
   *
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
   * Validate we get an exception when pausing tasks with an invalid level.
   *
   * @covers ::pause
   *
   * @depends testPause
   */
  public function testPauseFailLevel() {

    $action = new Task($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $action->pause(TRUE, ['level' => 'abc123']);
  }

  /**
   * Validate we can resume a Task.
   *
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

  /**
   * Validates we can wait for a task.
   *
   * @param int $expectedWait
   *   The expected wait time in seconds.
   * @param int $delay
   *   The delay between checks.
   * @param string $statusKey
   *   The status key to check against.
   *
   * @covers ::wait
   *
   * @dataProvider dpWaitIntervals
   *
   * @throws \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testWait(int $expectedWait, int $delay, $statusKey = NULL) {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $this->assertGreaterThanOrEqual(
      $expectedWait,
      $action->wait($delay, NULL, $statusKey)
    );
  }

  /**
   * Validate we can pass in a tick update function.
   *
   * @covers ::wait
   */
  public function testWaitCallable() {

    $action = new Task($this->getMockAcsfClient(), 1234);
    $callable_called = FALSE;
    $action->wait(
      1,
      function (EntityInterface $task, array $taskStatus) use (&$callable_called) {

        $callable_called = TRUE;
      }
    );
    $this->assertTrue($callable_called);
  }

  /**
   * Data provider for testWait().
   *
   * @return array
   *   An array of arguments to pass to testWait().
   */
  public function dpWaitIntervals(): array {

    return [
      [
        2,
        1,
        NULL,
      ],
      [
        2,
        -1,
        NULL,
      ],
      [
        2,
        0,
        NULL,
      ],
      [
        9,
        3,
        'error',
      ],
    ];
  }

  /**
   * Validates we get an exception when a key is not found.
   *
   * @covers ::wait
   *
   * @depends testWait
   */
  public function testWaitFailKey() {

    $action = new Task($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $action->wait(1, NULL, 'Abc' . time());
  }

}
