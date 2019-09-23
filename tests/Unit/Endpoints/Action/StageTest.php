<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Stage;
use swichers\Acsf\Client\Exceptions\InvalidEnvironmentException;

/**
 * Class StageTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Stage
 */
class StageTest extends ActionTestBase {

  /**
   * @covers ::getEnvironments
   */
  public function testGetEnvironments() {

    $action = new Stage($this->getMockAcsfClient());
    $this->assertEquals([
      'dev' => 'dev',
      'test' => 'test',
      'live' => 'live',
    ], $action->getEnvironments());

  }

  /**
   * @covers ::stage
   */
  public function testStage() {

    $action = new Stage($this->getMockAcsfClient());
    $options = [
      'synchronize_all_users' => 'yes',
      'detailed_status' => 'off',
      'wipe_target_environment' => 'true',
      'random_str' => FALSE,
    ];
    $result = $action->stage('dev', [123, 456, 'abc123'], $options);
    $this->assertTrue($result['json']['synchronize_all_users']);
    $this->assertFalse($result['json']['detailed_status']);
    $this->assertTrue($result['json']['wipe_target_environment']);
    $this->assertArrayNotHasKey('random_str', $result['json']);
    $this->assertEquals('dev', $result['json']['to_env']);
    $this->assertEquals([123, 456], $result['json']['sites']);
  }

  /**
   * @covers ::stage

   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidEnvironmentException
   */
  public function testStageEnvFail() {

    $action = new Stage($this->getMockAcsfClient());
    $action->stage('abc123', []);
  }
}
