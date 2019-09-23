<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\PageView;

/**
 * Class PageViewTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\PageView
 */
class PageViewTest extends ActionTestBase {

  /**
   * @covers ::getMonthlyDataByDomain
   * @covers ::genericDataRequest
   */
  public function testGetMonthlyDataByDomain() {

    $action = new PageView($this->mockClient);
    $result = $action->getMonthlyDataByDomain('1234-12', [
      'random_stuff' => TRUE,
      'limit' => -1,
      'stack_id' => -1,
      'sort_order' => 'asc',
    ]);
    $this->assertArrayNotHasKey('random_stuff', $result['query']);
    $this->assertEquals(1, $result['query']['limit']);
    $this->assertEquals(1, $result['query']['stack_id']);
    $this->assertEquals('dynamic-requests/monthly/domains', $result['internal_method']);

    $result = $action->getMonthlyDataByDomain('1234-12', [
      'limit' => 2,
      'stack_id' => 2,
    ]);
    $this->assertEquals(2, $result['query']['limit']);
    $this->assertEquals(2, $result['query']['stack_id']);

    $result = $action->getMonthlyDataByDomain('1234-56', ['sort_order' => 'abc123']);
    $this->assertEquals('desc', $result['query']['sort_order']);
  }

  /**
   * @covers ::getMonthlyDataByDomain
   * @covers ::genericDataRequest
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testGetMonthlyDataByDomainFailMonth() {

    $action = new PageView($this->mockClient);
    $action->getMonthlyDataByDomain('YYYY-MM');
  }

  /**
   * @covers ::getMonthlyData
   * @covers ::genericDataRequest
   */
  public function testGetMonthlyData() {

    $action = new PageView($this->mockClient);
    $result = $action->getMonthlyData([
      'start_from' => '1234-56',
      'random_stuff' => TRUE,
      'limit' => -1,
      'stack_id' => -1,
      'sort_order' => 'asc',
    ]);
    $this->assertArrayNotHasKey('random_stuff', $result['query']);
    $this->assertEquals(1, $result['query']['limit']);
    $this->assertEquals(1, $result['query']['stack_id']);
    $this->assertEquals('dynamic-requests/monthly', $result['internal_method']);

    $result = $action->getMonthlyData([
      'limit' => 2,
      'stack_id' => 2,
    ]);
    $this->assertEquals(2, $result['query']['limit']);
    $this->assertEquals(2, $result['query']['stack_id']);

    $result = $action->getMonthlyData(['sort_order' => 'abc123']);
    $this->assertEquals('desc', $result['query']['sort_order']);
  }

  /**
   * @covers ::getMonthlyData
   * @covers ::genericDataRequest
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testGetMonthlyDataFailMonth() {

    $action = new PageView($this->mockClient);
    $action->getMonthlyData(['start_from' => 'abc123']);
  }

}
