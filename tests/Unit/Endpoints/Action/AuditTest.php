<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Audit;

/**
 * Tests for the AuditTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Audit
 *
 * @group AcsfClient
 */
class AuditTest extends AbstractActionTestBase {

  /**
   * Validate we can list audit logs.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Audit($this->getMockAcsfClient());
    $this->assertSharedListValidation('audit', $action, 'list');
  }

}
