<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Stacks;
use PHPUnit\Framework\TestCase;

/**
 * Class StacksTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Stacks
 */
class StacksTest extends ActionTestBase {

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Stacks($this->getMockAcsfClient());
    $result = $action->list(['random_str' => TRUE, 'stack_id' => 10]);
    $this->assertEquals('stacks', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }
}
