<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Site;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class SiteTest
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Site
 */
class SiteTest extends TestCase {

  use AcsfClientTrait;

  /**
   * @covers ::duplicate
   */
  public function testDuplicate() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $options = [
      'random' => TRUE,
      'group_ids' => [123, '456', 'abc123'],
      'exact_copy' => 'yes',
    ];
    $result = $action->duplicate('UnitTest', $options);
    $this->assertEquals('sites/1234/duplicate', $result['internal_method']);
    $this->assertEquals('UnitTest', $result['json']['site_name']);
    $this->assertArrayNotHasKey('random', $result['json']);
    $this->assertTrue($result['json']['exact_copy']);
    $this->assertEquals([123, 456], $result['json']['group_ids']);

    $result = $action->duplicate('UnitTest');
    $this->assertArrayNotHasKey('group_ids', $result['json']);
  }

  /**
   * @covers ::details
   */
  public function testDetails() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->details();
    $this->assertEquals('sites/1234', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * @covers ::delete
   */
  public function testDelete() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->delete();
    $this->assertEquals('sites/1234', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * @covers ::backup
   */
  public function testBackup() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $options = [
      'random' => TRUE,
      'label' => 'UnitTest',
      'callback_url' => 'http://example.com',
      'callback_method' => 'GET',
      'caller_data' => ['test' => TRUE],
      'components' => ['database'],
    ];
    $result = $action->backup($options);
    $this->assertEquals('sites/1234/backup', $result['internal_method']);
    $this->assertArrayNotHasKey('random', $result['json']);
    $this->assertEquals('UnitTest', $result['json']['label']);
    $this->assertEquals('http://example.com', $result['json']['callback_url']);
    $this->assertEquals('GET', $result['json']['callback_method']);
    $this->assertEquals(json_encode(['test' => TRUE]), $result['json']['caller_data']);
    $this->assertEquals(['database'], $result['json']['components']);

    $components = [
      'database',
      'themes',
      'public files',
      'private files',
    ];
    $result = $action->backup(['components' => $components]);
    $this->assertEquals($components, $result['json']['components']);

    $result = $action->backup(['callback_method' => 'POST']);
    $this->assertEquals('POST', $result['json']['callback_method']);
  }

  /**
   * @covers ::backup
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testBackupFailCallbackUrl() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $action->backup(['callback_url' => 'any random invalid url']);
  }

  /**
   * @covers ::backup
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testBackupFailCallbackMethod() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $action->backup(['callback_method' => 'any random invalid method']);
  }

  /**
   * @covers ::backup
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testBackupFailBadComponentsDataType() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $action->backup(['components' => 'any random invalid data type']);
  }

  /**
   * @covers ::backup
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testBackupFailBadComponentsDataValue() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $action->backup([
      'components' => [
        'any',
        'random',
        'invalid',
        'components',
        'values',
      ],
    ]);
  }

  /**
   * @covers ::listBackups
   */
  public function testListBackups() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $options = [
      'random' => TRUE,
      'limit' => -1,
      'page' => -1,
    ];
    $result = $action->listBackups($options);
    $this->assertEquals('sites/1234/backups', $result['internal_method']);
    $this->assertEquals(1, $result['query']['limit']);
    $this->assertEquals(1, $result['query']['page']);
    $this->assertArrayNotHasKey('random', $result['query']);

    $result = $action->listBackups(['limit' => 20, 'page' => 3]);
    $this->assertEquals(20, $result['query']['limit']);
    $this->assertEquals(3, $result['query']['page']);
  }

  /**
   * @covers ::clearCache
   */
  public function testClearCache() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->clearCache();
    $this->assertEquals('sites/1234/cache-clear', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * @covers ::getDomains
   */
  public function testGetDomains() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->getDomains();
    $this->assertEquals('domains/1234', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * @covers ::addDomain
   */
  public function testAddDomain() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->addDomain('UnitTest');
    $this->assertEquals('domains/1234/add', $result['internal_method']);
    $this->assertEquals(['domain_name' => 'UnitTest'], $result['json']);
  }

  /**
   * @covers ::removeDomain
   */
  public function testRemoveDomain() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->removeDomain('UnitTest');
    $this->assertEquals('domains/1234/remove', $result['internal_method']);
    $this->assertEquals(['domain_name' => 'UnitTest'], $result['json']);
  }

}
