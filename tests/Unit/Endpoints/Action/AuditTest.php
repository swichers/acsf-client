<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Audit;

/**
 * Class AuditTest
 *
 * @package swichers\Acsf\Tests\Client\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Audit
 */
class AuditTest extends ActionTestBase {

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Audit($this->getMockAcsfClient());
    $this->assertSharedListValidation('audit', $action, 'list');
  }

}
