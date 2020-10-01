<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Site;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Site entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Site
 *
 * @group AcsfClient
 */
class SiteTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validates we can duplicate a Site.
   *
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
   * Validates we can get Site details.
   *
   * @covers ::details
   */
  public function testDetails() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->details();
    $this->assertEquals('sites/1234', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * Validates we can delete a Site.
   *
   * @covers ::delete
   */
  public function testDelete() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->delete();
    $this->assertEquals('sites/1234', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * Validates we can backup a Site.
   *
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
    $this->assertEquals(
      json_encode(['test' => TRUE]),
      $result['json']['caller_data']
    );
    $this->assertEquals(['database'], $result['json']['components']);

    $components = [
      'codebase',
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
   * Validates we get an exception when callback_urls are invalid for backups.
   *
   * @covers ::backup
   *
   * @depends testBackup
   */
  public function testBackupFailCallbackUrl() {

    $action = new Site($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $action->backup(['callback_url' => 'any random invalid url']);
  }

  /**
   * Validates we get an exception for invalid backup callback methods.
   *
   * @covers ::backup
   *
   * @depends testBackup
   */
  public function testBackupFailCallbackMethod() {

    $action = new Site($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $action->backup(['callback_method' => 'any random invalid method']);
  }

  /**
   * Validates we get an exception when backups are given bad component data.
   *
   * @covers ::backup
   *
   * @depends testBackup
   */
  public function testBackupFailBadComponentsDataType() {

    $action = new Site($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $action->backup(['components' => 'any random invalid data type']);
  }

  /**
   * Validates we get an exception when backups are given an invalid component.
   *
   * @covers ::backup
   *
   * @depends testBackup
   */
  public function testBackupFailBadComponentsDataValue() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $this->expectException(InvalidOptionException::class);
    $action->backup(
      [
        'components' => [
          'any',
          'random',
          'invalid',
          'components',
          'values',
        ],
      ]
    );
  }

  /**
   * Validates we can list backups.
   *
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
   * Validates we can clear cache.
   *
   * @covers ::clearCache
   */
  public function testClearCache() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->clearCache();
    $this->assertEquals('sites/1234/cache-clear', $result['internal_method']);
    $this->assertEmpty($result['json']);
  }

  /**
   * Validates we can get domains.
   *
   * @covers ::getDomains
   */
  public function testGetDomains() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->getDomains();
    $this->assertEquals('domains/1234', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * Validates we can add a domain.
   *
   * @covers ::addDomain
   */
  public function testAddDomain() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->addDomain('UnitTest');
    $this->assertEquals('domains/1234/add', $result['internal_method']);
    $this->assertEquals(['domain_name' => 'UnitTest'], $result['json']);
  }

  /**
   * Validates we can remove a domain.
   *
   * @covers ::removeDomain
   */
  public function testRemoveDomain() {

    $action = new Site($this->getMockAcsfClient(), 1234);
    $result = $action->removeDomain('UnitTest');
    $this->assertEquals('domains/1234/remove', $result['internal_method']);
    $this->assertEquals(['domain_name' => 'UnitTest'], $result['json']);
  }

}
