<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Vcs;

/**
 * Tests for the VcsTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Vcs
 *
 * @group AcsfClient
 */
class VcsTest extends AbstractActionTestBase {

  /**
   * Validates we can list Vcs items.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Vcs($this->getMockAcsfClient());
    $options = [
      'stack_id' => -1,
      'random' => TRUE,
    ];

    $result = $action->list($options);
    $this->assertEquals('vcs', $result['internal_method']);
    $this->assertEquals(1, $result['query']['stack_id']);
    $this->assertArrayNotHasKey('random', $result['query']);
    $result = $action->list(['stack_id' => 3]);
    $this->assertEquals(3, $result['query']['stack_id']);
  }

}
