<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\PageView;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

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

    $pv = new PageView($this->mockClient);
    $result = $pv->getMonthlyDataByDomain('1234-12', [
      'random_stuff' => TRUE,
      'limit' => -1,
      'stack_id' => -1,
      'sort_order' => 'asc',
    ]);
    $this->assertArrayNotHasKey('random_stuff', $result);
    $this->assertEquals(1, $result['limit']);
    $this->assertEquals(1, $result['stack_id']);
    $this->assertEquals('dynamic-requests/monthly/domains', $result['internal_method']);

    $result = $pv->getMonthlyDataByDomain('1234-12', [
      'limit' => 2,
      'stack_id' => 2,
    ]);
    $this->assertEquals(2, $result['limit']);
    $this->assertEquals(2, $result['stack_id']);

    $this->expectException(InvalidOptionException::class);
    $pv->getMonthlyDataByDomain('YYYY-MM');
    $this->expectException(InvalidOptionException::class);
    $pv->getMonthlyDataByDomain('1234-56', ['sort_order' => 'abc123']);
  }

  /**
   * @covers ::getMonthlyData
   * @covers ::genericDataRequest
   */
  public function testGetMonthlyData() {

    $pv = new PageView($this->mockClient);
    $result = $pv->getMonthlyData([
      'start_from' => '1234-56',
      'random_stuff' => TRUE,
      'limit' => -1,
      'stack_id' => -1,
      'sort_order' => 'asc',
    ]);
    $this->assertArrayNotHasKey('random_stuff', $result);
    $this->assertEquals(1, $result['limit']);
    $this->assertEquals(1, $result['stack_id']);
    $this->assertEquals('dynamic-requests/monthly', $result['internal_method']);

    $result = $pv->getMonthlyData([
      'limit' => 2,
      'stack_id' => 2,
    ]);
    $this->assertEquals(2, $result['limit']);
    $this->assertEquals(2, $result['stack_id']);

    $this->expectException(InvalidOptionException::class);
    $pv->getMonthlyData(['start_from' => 'abc123']);
    $this->expectException(InvalidOptionException::class);
    $pv->getMonthlyData(['sort_order' => 'abc123']);
  }

}
