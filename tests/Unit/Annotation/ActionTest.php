<?php

namespace swichers\Acsf\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Action;

/**
 * Class ActionTest
 * @coversDefaultClass \swichers\Acsf\Client\Annotation\Action
 */
class ActionTest extends TestCase {

  /**
   * @covers ::getName
   */
  public function testGetName() {
    $action = new Action();
    $action->name = 'UnitTest';
    $this->assertEquals($action->name, $action->getName());
  }
}
