<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Tasks;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Tests for the TasksTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Tasks
 *
 * @group AcsfClient
 */
class TasksTest extends AbstractActionTestBase {

  /**
   * Validate we can list Tasks.
   *
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
   * Validate we get an exception with a bad List status.
   *
   * @covers ::list
   *
   * @depends testList
   */
  public function testListFailStatus() {

    $action = new Tasks($this->getMockAcsfClient());

    $this->expectException(InvalidOptionException::class);
    $action->list(['status' => 'abc123']);
  }

  /**
   * Validate we get an exception with a bad List class.
   *
   * @covers ::list
   *
   * @depends testList
   */
  public function testListFailClass() {

    $action = new Tasks($this->getMockAcsfClient());

    $this->expectException(InvalidOptionException::class);
    $action->list(['class' => 'abc123']);
  }

  /**
   * Validate we can pause Task processing.
   *
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
   * Validate we can get a Task entity type.
   *
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Tasks($this->getMockAcsfClient());
    $this->assertSame('Task', $action->getEntityType());
  }

  /**
   * Validate we can get info by class type.
   *
   * @covers ::getClassInfo
   */
  public function testGetClassInfo() {

    $action = new Tasks($this->getMockAcsfClient());
    $result = $action->getClassInfo('softpaused');
    $this->assertEquals('classes/softpaused', $result['internal_method']);
    $result = $action->getClassInfo('softpause-for-update');
    $this->assertEquals(
      'classes/softpause-for-update',
      $result['internal_method']
    );
  }

  /**
   * Validate we get an exception when given a bad class type.
   *
   * @covers ::getClassInfo
   *
   * @depends testGetClassInfo
   */
  public function testGetClassInfoFailClassType() {

    $action = new Tasks($this->getMockAcsfClient());

    $this->expectException(InvalidOptionException::class);
    $action->getClassInfo('abc123');
  }

}
