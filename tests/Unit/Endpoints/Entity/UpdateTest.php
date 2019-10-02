<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Update;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Update entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Update
 *
 * @group AcsfClient
 */
class UpdateTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can pause an update.
   *
   * @covers ::pause
   */
  public function testPause() {

    $entity = new Update($this->getMockAcsfClient(), 1234);
    $result = $entity->pause(TRUE);
    $this->assertEquals('update/1234/pause', $result['internal_method']);
    $this->assertTrue($result['json']['pause']);

    $result = $entity->pause(FALSE);
    $this->assertEquals('update/1234/pause', $result['internal_method']);
    $this->assertFalse($result['json']['pause']);
  }

  /**
   * Validate we can get the status of an update.
   *
   * @covers ::progress
   */
  public function testProgress() {

    $entity = new Update($this->getMockAcsfClient(), 1234);
    $result = $entity->progress();
    $this->assertEquals('update/1234/status', $result['internal_method']);
  }

  /**
   * Validate we can resume an update.
   *
   * @covers ::resume
   *
   * @depends testPause
   */
  public function testResume() {

    $entity = new Update($this->getMockAcsfClient(), 1234);
    $result = $entity->resume();
    $this->assertEquals('update/1234/pause', $result['internal_method']);
    $this->assertFalse($result['json']['pause']);
  }

}
