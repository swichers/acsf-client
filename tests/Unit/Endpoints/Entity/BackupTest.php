<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\Entity\Backup;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Site;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class BackupTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Entity
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Backup
 */
class BackupTest extends TestCase {

  use AcsfClientTrait;

  /**
   * @covers ::getUrl
   */
  public function testGetUrl() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $result = $action->getUrl(['lifetime' => 1234567890, 'random' => TRUE]);
    $this->assertEquals('sites/5678/backups/1234/url', $result['internal_method']);
    $this->assertArrayNotHasKey('random', $result['query']);
    $this->assertEquals(1234567890, $result['query']['lifetime']);

    $result = $action->getUrl(['lifetime' => -1,]);
    $this->assertEquals(1, $result['query']['lifetime']);
    $result = $action->getUrl();
    $this->assertArrayNotHasKey('lifetime', $result['query']);
  }

  /**
   * @covers ::delete
   */
  public function testDelete() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $options = [
      'random' => TRUE,
      'callback_url' => 'http://example.com',
      'callback_method' => 'GET',
      'caller_data' => ['test' => TRUE],
    ];
    $result = $action->delete($options);
    $this->assertEquals('sites/5678/backups/1234', $result['internal_method']);
    $this->assertArrayNotHasKey('random', $result['json']);
    $this->assertEquals('http://example.com', $result['json']['callback_url']);
    $this->assertEquals('GET', $result['json']['callback_method']);
    $this->assertEquals(json_encode(['test' => TRUE]), $result['json']['caller_data']);

    $result = $action->delete(['callback_method' => 'POST']);
    $this->assertEquals('POST', $result['json']['callback_method']);
  }

  /**
   * @covers ::delete
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testDeleteFailCallbackUrl() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $action->delete(['callback_url' => 'any random invalid url']);
  }

  /**
   * @covers ::delete
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testDeleteFailCallbackMethod() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $action->delete(['callback_method' => 'any random invalid method']);
  }

  /**
   * @covers ::restore
   */
  public function testRestore() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $options = [
      'random' => TRUE,
      'callback_url' => 'http://example.com',
      'callback_method' => 'GET',
      'caller_data' => ['test' => TRUE],
      'components' => ['database'],
    ];
    $result = $action->restore($options);
    $this->assertEquals('sites/5678/restore', $result['internal_method']);
    $this->assertArrayNotHasKey('random', $result['json']);
    $this->assertEquals(5678, $result['json']['target_site_id']);
    $this->assertEquals(1234, $result['json']['backup_id']);
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
    $result = $action->restore(['components' => $components]);
    $this->assertEquals($components, $result['json']['components']);

    $result = $action->restore(['callback_method' => 'POST']);
    $this->assertEquals('POST', $result['json']['callback_method']);
  }


  /**
   * @covers ::restore
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRestoreFailCallbackUrl() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $action->restore(['callback_url' => 'any random invalid url']);
  }

  /**
   * @covers ::restore
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRestoreFailCallbackMethod() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $action->restore(['callback_method' => 'any random invalid method']);
  }

  /**
   * @covers ::restore
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRestoreFailBadComponentsDataType() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $action->restore(['components' => 'any random invalid data type']);
  }

  /**
   * @covers ::restore
   * @expectedException  \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRestoreFailBadComponentsDataValue() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $action->restore([
      'components' => [
        'any',
        'random',
        'invalid',
        'components',
        'values',
      ],
    ]);
  }


}
