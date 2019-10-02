<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Stage;
use swichers\Acsf\Client\Exceptions\InvalidEnvironmentException;

/**
 * Tests for the Stage Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Stage
 *
 * @group AcsfClient
 */
class StageTest extends AbstractActionTestBase {

  /**
   * Validates we can get a list of environments.
   *
   * @covers ::getEnvironments
   */
  public function testGetEnvironments() {

    $action = new Stage($this->getMockAcsfClient());
    $this->assertEquals(
      [
        'dev' => 'dev',
        'test' => 'test',
        'live' => 'live',
      ],
      $action->getEnvironments()
    );

  }

  /**
   * Validates we can backport environments.
   *
   * @covers ::backport
   */
  public function testBackport() {

    $action = new Stage($this->getMockAcsfClient());
    $options = [
      'synchronize_all_users' => 'yes',
      'detailed_status' => 'off',
      'wipe_target_environment' => 'true',
      'random_str' => FALSE,
    ];
    $result = $action->backport('dev', [123, 456, 'abc123'], $options);
    $this->assertTrue($result['json']['synchronize_all_users']);
    $this->assertFalse($result['json']['detailed_status']);
    $this->assertTrue($result['json']['wipe_target_environment']);
    $this->assertArrayNotHasKey('random_str', $result['json']);
    $this->assertEquals('dev', $result['json']['to_env']);
    $this->assertEquals([123, 456], $result['json']['sites']);
  }

  /**
   * Validate we get an exception when trying to stage an invalid environment.
   *
   * @covers ::backport
   *
   * @depends testBackport
   */
  public function testBackportEnvFail() {

    $action = new Stage($this->getMockAcsfClient());

    $this->expectException(InvalidEnvironmentException::class);
    $action->backport('abc123', []);
  }

}
