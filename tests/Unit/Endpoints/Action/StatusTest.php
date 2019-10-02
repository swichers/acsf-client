<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Status;

/**
 * Tests for the StatusTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Status
 *
 * @group AcsfClient
 */
class StatusTest extends AbstractActionTestBase {

  /**
   * Validate we can get ACSF info.
   *
   * @covers ::getSiteFactoryInfo
   */
  public function testGetSiteFactoryInfo() {

    $action = new Status($this->getMockAcsfClient());
    $result = $action->getSiteFactoryInfo();
    $this->assertEquals('sf-info', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * Validate we can execute a ping request.
   *
   * @covers ::ping
   */
  public function testPing() {

    $action = new Status($this->getMockAcsfClient());
    $result = $action->ping();
    $this->assertEquals('ping', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * Validate we can get a status report.
   *
   * @covers ::get
   */
  public function testGet() {

    $action = new Status($this->getMockAcsfClient());
    $result = $action->get();
    $this->assertEquals('status', $result['internal_method']);
    $this->assertEmpty($result['query']);

  }

  /**
   * Validate we can set ACSF service status.
   *
   * @covers ::set
   */
  public function testSet() {

    $action = new Status($this->getMockAcsfClient());
    $options = [
      'all' => 'no',
      'site_creation' => 'yes',
      'site_duplication' => 'tomorrow +3 days',
      'domain_management' => 'now',
      'bulk_operations' => 1234567890,
      'random' => TRUE,
    ];
    $result = $action->set($options);
    $this->assertEquals('status', $result['internal_method']);
    $this->assertFalse($result['json']['all']);
    $this->assertTrue($result['json']['site_creation']);
    $this->assertEquals(
      'tomorrow +3 days',
      $result['json']['site_duplication']
    );
    $this->assertEquals('now', $result['json']['domain_management']);
    $this->assertEquals(1234567890, $result['json']['bulk_operations']);
    $this->assertArrayNotHasKey('random', $result['json']);
  }

}
