<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Theme;

/**
 * Class ThemeTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Theme
 */
class ThemeTest extends ActionTestBase {

  /**
   * @covers ::process
   */
  public function testProcess() {

    $action = new Theme($this->getMockAcsfClient());
    $result = $action->process(['sitegroup_id' => 123, 'random' => TRUE]);
    $this->assertEquals('theme/process', $result['internal_method']);
    $this->assertEquals(123, $result['json']['sitegroup_id']);
    $this->assertArrayNotHasKey('random', $result['json']);

    $this->assertEmpty($action->process(['random' => TRUE])['json']);
  }

  /**
   * @covers ::deploy
   */
  public function testDeploy() {

    $action = new Theme($this->getMockAcsfClient());
    $options = [
      'sitegroup' => 'abc',
      'webnode' => 'def',
      'random' => TRUE,
    ];

    $result = $action->deploy($options);
    $this->assertEquals('theme/deploy', $result['internal_method']);
    $this->assertEquals('abc', $result['json']['sitegroup']);
    $this->assertEquals('def', $result['json']['webnode']);
    $this->assertArrayNotHasKey('random', $result['json']);

    $this->assertEmpty($action->deploy(['random' => TRUE])['json']);
  }

  /**
   * @covers ::sendNotification
   */
  public function testSendNotification() {

    $action = new Theme($this->getMockAcsfClient());
    $options = [
      'random' => TRUE,
      'timestamp' => 1234567890,
      'uid' => 2000,
      'nid' => 1000,
      'theme' => 'UnitTheme',
    ];

    $result = $action->sendNotification('theme', 'create', $options);
    $this->assertEquals('theme/notification', $result['internal_method']);
    $this->assertEquals(2000, $result['json']['uid']);
    $this->assertEquals(1000, $result['json']['nid']);
    $this->assertEquals(1234567890, $result['json']['timestamp']);
    $this->assertEquals('UnitTheme', $result['json']['theme']);
    $this->assertArrayNotHasKey('random', $result['json']);

    $this->assertArrayNotHasKey('nid', $action->sendNotification('global', 'create', ['nid' => 1000])['json']);
    $this->assertArrayHasKey('nid', $action->sendNotification('group', 'create', ['nid' => 1000])['json']);
    $this->assertArrayHasKey('nid', $action->sendNotification('site', 'create', ['nid' => 1000])['json']);
    $this->assertArrayHasKey('nid', $action->sendNotification('theme', 'create', ['nid' => 1000])['json']);

    $this->assertArrayHasKey('theme', $action->sendNotification('theme', 'create', ['theme' => 'UnitTheme'])['json']);
    $this->assertArrayNotHasKey('theme', $action->sendNotification('group', 'create', ['theme' => 'UnitTheme'])['json']);
    $this->assertArrayNotHasKey('theme', $action->sendNotification('site', 'create', ['theme' => 'UnitTheme'])['json']);
    $this->assertArrayNotHasKey('theme', $action->sendNotification('global', 'create', ['theme' => 'UnitTheme'])['json']);

    $this->assertEquals('theme', $action->sendNotification('theme', 'create', $options)['json']['scope']);
    $this->assertEquals('site', $action->sendNotification('site', 'create', $options)['json']['scope']);
    $this->assertEquals('group', $action->sendNotification('group', 'create', $options)['json']['scope']);
    $this->assertEquals('global', $action->sendNotification('global', 'create', $options)['json']['scope']);

    $this->assertEquals('create', $action->sendNotification('theme', 'create', $options)['json']['event']);
    $this->assertEquals('modify', $action->sendNotification('theme', 'modify', $options)['json']['event']);
    $this->assertEquals('delete', $action->sendNotification('theme', 'delete', $options)['json']['event']);
  }

  /**
   * @covers ::sendNotification
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testSendNotificationFailScope() {

    $action = new Theme($this->getMockAcsfClient());
    $action->sendNotification('abc123', 'create');
  }

  /**
   * @covers ::sendNotification
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testSendNotificationFailEvent() {

    $action = new Theme($this->getMockAcsfClient());
    $action->sendNotification('theme', 'abc123');
  }

}
