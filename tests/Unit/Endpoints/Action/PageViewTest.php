<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\PageView;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Tests for the PageViewTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\PageView
 *
 * @group AcsfClient
 */
class PageViewTest extends AbstractActionTestBase {

  /**
   * Validate we can get the monthly data by domain report.
   *
   * @covers ::getMonthlyDataByDomain
   * @covers ::genericDataRequest
   */
  public function testGetMonthlyDataByDomain() {

    $action = new PageView($this->mockClient);
    $result = $action->getMonthlyDataByDomain(
      '1234-12',
      [
        'random_stuff' => TRUE,
        'limit' => -1,
        'stack_id' => -1,
        'sort_order' => 'asc',
      ]
    );
    $this->assertArrayNotHasKey('random_stuff', $result['query']);
    $this->assertEquals(1, $result['query']['limit']);
    $this->assertEquals(1, $result['query']['stack_id']);
    $this->assertEquals(
      'dynamic-requests/monthly/domains',
      $result['internal_method']
    );

    $result = $action->getMonthlyDataByDomain(
      '1234-12',
      [
        'limit' => 2,
        'stack_id' => 2,
      ]
    );
    $this->assertEquals(2, $result['query']['limit']);
    $this->assertEquals(2, $result['query']['stack_id']);

    $result = $action->getMonthlyDataByDomain(
      '1234-56',
      ['sort_order' => 'abc123']
    );
    $this->assertEquals('desc', $result['query']['sort_order']);
  }

  /**
   * Validate the monthly domain report throws an exception with a bad date.
   *
   * @covers ::getMonthlyDataByDomain
   * @covers ::genericDataRequest
   *
   * @depends testGetMonthlyDataByDomain
   */
  public function testGetMonthlyDataByDomainFailMonth() {

    $action = new PageView($this->mockClient);

    $this->expectException(InvalidOptionException::class);
    $action->getMonthlyDataByDomain('YYYY-MM');
  }

  /**
   * Validate we can get a monthly data report.
   *
   * @covers ::getMonthlyData
   * @covers ::genericDataRequest
   */
  public function testGetMonthlyData() {

    $action = new PageView($this->mockClient);
    $result = $action->getMonthlyData(
      [
        'start_from' => '1234-56',
        'random_stuff' => TRUE,
        'limit' => -1,
        'stack_id' => -1,
        'sort_order' => 'asc',
      ]
    );
    $this->assertArrayNotHasKey('random_stuff', $result['query']);
    $this->assertEquals(1, $result['query']['limit']);
    $this->assertEquals(1, $result['query']['stack_id']);
    $this->assertEquals('dynamic-requests/monthly', $result['internal_method']);

    $result = $action->getMonthlyData(
      [
        'limit' => 2,
        'stack_id' => 2,
      ]
    );
    $this->assertEquals(2, $result['query']['limit']);
    $this->assertEquals(2, $result['query']['stack_id']);

    $result = $action->getMonthlyData(['sort_order' => 'abc123']);
    $this->assertEquals('desc', $result['query']['sort_order']);
  }

  /**
   * Validate the monthly data report throws an exception with a bad start date.
   *
   * @covers ::getMonthlyData
   * @covers ::genericDataRequest
   *
   * @depends testGetMonthlyData
   */
  public function testGetMonthlyDataFailMonth() {

    $action = new PageView($this->mockClient);

    $this->expectException(InvalidOptionException::class);
    $action->getMonthlyData(['start_from' => 'abc123']);
  }

}
