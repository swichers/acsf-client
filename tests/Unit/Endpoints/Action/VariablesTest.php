<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Variables;

/**
 * Class VariablesTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Variables
 */
class VariablesTest extends ActionTestBase {

  /**
   * @covers ::get
   */
  public function testGet() {

    $action = new Variables($this->getMockAcsfClient());
    $result = $action->get('UnitTest');
    $this->assertEquals('variables', $result['internal_method']);
    $this->assertEquals(['name' => 'UnitTest'], $result['query']);
  }

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Variables($this->getMockAcsfClient());
    $options = [
      'search' => 'anything',
      'random' => TRUE,
    ];
    $result = $action->list($options);
    $this->assertEquals('variables', $result['internal_method']);
    $this->assertEquals('anything', $result['query']['search']);
    $this->assertArrayNotHasKey('random', $result['query']);

    $result = $action->list();
    $this->assertEmpty($result['query']);
  }

  /**
   * @covers ::set
   */
  public function testSet() {

    $action = new Variables($this->getMockAcsfClient());
    $result = $action->set('Unit', 'Test');
    $this->assertEquals('variables', $result['internal_method']);
    $this->assertEquals(['name' => 'Unit', 'value' => 'Test'], $result['json']);
  }

}
