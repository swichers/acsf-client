<?php


namespace swichers\Acsf\Client\Tests\Endpoints\Action;


use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

abstract class ActionTestBase extends TestCase {

  use AcsfClientTrait;

  protected function setUp() {

    parent::setUp();

    $this->mockClient = $this->getMockAcsfClient();
  }

  protected function assertSharedListValidation($path, $object, $method) {

    // No options means no options
    $this->assertEquals([
      'internal_method' => $path,
      'query' => [],
    ], $object->{$method}());

    $result = $object->{$method}(['random_test' => TRUE, 'limit' => -1]);
    // We're pruning keys.
    $this->assertArrayNotHasKey('random_test', $result['query']);

    // We're enforcing paging.
    $this->assertEquals(1, $result['query']['limit']);
  }
}
