<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Backup;
use swichers\Acsf\Client\Endpoints\Entity\Site;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Backup entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Backup
 *
 * @group AcsfClient
 */
class BackupTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can get a URL for Backups.
   *
   * @covers ::getUrl
   */
  public function testGetUrl() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);
    $result = $action->getUrl(['lifetime' => 1234567890, 'random' => TRUE]);
    $this->assertEquals(
      'sites/5678/backups/1234/url',
      $result['internal_method']
    );
    $this->assertArrayNotHasKey('random', $result['query']);
    $this->assertEquals(1234567890, $result['query']['lifetime']);

    $result = $action->getUrl(['lifetime' => -1]);
    $this->assertEquals(1, $result['query']['lifetime']);
    $result = $action->getUrl();
    $this->assertArrayNotHasKey('lifetime', $result['query']);
  }

  /**
   * Validate we can delete a Backup.
   *
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
    $this->assertEquals(
      json_encode(['test' => TRUE]),
      $result['json']['caller_data']
    );

    $result = $action->delete(['callback_method' => 'POST']);
    $this->assertEquals('POST', $result['json']['callback_method']);
  }

  /**
   * Validate we get an exception when deleting with a bad callback_url.
   *
   * @covers ::delete
   *
   * @depends testDelete
   */
  public function testDeleteFailCallbackUrl() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);

    $this->expectException(InvalidOptionException::class);
    $action->delete(['callback_url' => 'any random invalid url']);
  }

  /**
   * Validate we get an exception when deleting with a bad callback_method.
   *
   * @covers ::delete
   *
   * @depends testDelete
   */
  public function testDeleteFailCallbackMethod() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);

    $this->expectException(InvalidOptionException::class);
    $action->delete(['callback_method' => 'any random invalid method']);
  }

  /**
   * Validate we can restore a Backup.
   *
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
    $this->assertEquals(
      json_encode(['test' => TRUE]),
      $result['json']['caller_data']
    );
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
   * Validate we get an exception when restoring with a bad callback_url.
   *
   * @covers ::restore
   *
   * @depends testRestore
   */
  public function testRestoreFailCallbackUrl() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);

    $this->expectException(InvalidOptionException::class);
    $action->restore(['callback_url' => 'any random invalid url']);
  }

  /**
   * Validate we get an exception when restoring with a bad callback_method.
   *
   * @covers ::restore
   *
   * @depends testRestore
   */
  public function testRestoreFailCallbackMethod() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);

    $this->expectException(InvalidOptionException::class);
    $action->restore(['callback_method' => 'any random invalid method']);
  }

  /**
   * Validate we get an exception when restoring with a bad components type.
   *
   * @covers ::restore
   *
   * @depends testRestore
   */
  public function testRestoreFailBadComponentsDataType() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);

    $this->expectException(InvalidOptionException::class);
    $action->restore(['components' => 'any random invalid data type']);
  }

  /**
   * Validate we get an exception when restoring with a bad components value.
   *
   * @covers ::restore
   *
   * @depends testRestore
   */
  public function testRestoreFailBadComponentsDataValue() {

    $site = new Site($this->getMockAcsfClient(), 5678);
    $action = new Backup($this->getMockAcsfClient(), 1234, $site);

    $this->expectException(InvalidOptionException::class);
    $action->restore(
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

}
