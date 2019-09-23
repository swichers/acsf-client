<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Update;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class UpdateTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Entity
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Update
 */
class UpdateTest extends TestCase {

  use AcsfClientTrait;

  /**
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
   * @covers ::progress
   */
  public function testProgress() {

    $entity = new Update($this->getMockAcsfClient(), 1234);
    $result = $entity->progress();
    $this->assertEquals('update/1234/status', $result['internal_method']);
  }

  /**
   * @covers ::resume
   */
  public function testResume() {

    $entity = new Update($this->getMockAcsfClient(), 1234);
    $result = $entity->resume();
    $this->assertEquals('update/1234/pause', $result['internal_method']);
    $this->assertFalse($result['json']['pause']);
  }

}
