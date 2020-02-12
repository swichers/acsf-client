<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Base class for testing ACSF Client Actions.
 *
 * @group AcsfClient
 */
abstract class AbstractActionTestBase extends TestCase {

  use AcsfClientTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {

    parent::setUp();

    $this->mockClient = $this->getMockAcsfClient();
  }

  /**
   * Shared test asserts for general option list checking.
   *
   * @param string $apiPath
   *   The API endpoint path.
   * @param \swichers\Acsf\Client\Endpoints\Action\ActionInterface $action
   *   The Action to validate option information for.
   * @param string $method
   *   The method to execute on the Action object.
   */
  protected function assertSharedListValidation(string $apiPath, ActionInterface $action, string $method) {

    // No options means no options.
    $this->assertEquals(
      [
        'internal_method' => $apiPath,
        'query' => [],
      ],
      $action->{$method}()
    );

    $result = $action->{$method}(['random_test' => TRUE, 'limit' => -1]);
    // We're pruning keys.
    $this->assertArrayNotHasKey('random_test', $result['query']);

    // We're enforcing paging.
    $this->assertEquals(1, $result['query']['limit']);
  }

}
