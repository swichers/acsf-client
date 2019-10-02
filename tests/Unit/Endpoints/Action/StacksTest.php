<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Stacks;

/**
 * Tests for the StacksTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Stacks
 *
 * @group AcsfClient
 */
class StacksTest extends AbstractActionTestBase {

  /**
   * Validate we can get a list of Stacks.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Stacks($this->getMockAcsfClient());
    $result = $action->list(['random_str' => TRUE, 'stack_id' => 10]);
    $this->assertEquals('stacks', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

}
