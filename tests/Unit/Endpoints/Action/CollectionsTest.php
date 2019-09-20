<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Collections;
use swichers\Acsf\Client\Tests\Traits\SharedListChecks;

/**
 * Class CollectionsTest
 *
 * @package swichers\Acsf\Tests\Client\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Collections
 */
class CollectionsTest extends ActionTestBase {

  /**
   * @covers ::list
   */
  public function testList() {

    $action = new Collections($this->mockClient);
    $this->assertSharedListValidation('collections', $action, 'list');
  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Collections($this->mockClient);
    $this->assertSame('Collection', $action->getEntityType());
  }

  /**
   * @covers ::create
   */
  public function testCreate() {

    $action = new Collections($this->mockClient);

    $result = $action->create('Test', [123], [456], []);
    $this->assertEquals('collections', $result['internal_method']);

    // No options means no options
    $expected = [
      'name' => 'Test',
      'site_ids' => [123],
      'group_ids' => [456],
    ];
    $this->assertEquals($expected, $result['json']);

    // We're pruning keys.
    $result = $action->create('Test', [123], [456], [
      'random_str' => TRUE,
      'internal_domain_prefix' => 'xyz',
    ]);
    $this->assertArrayNotHasKey('random_test', $result['json']);
    $this->assertEquals('xyz', $result['json']['internal_domain_prefix']);

    // We're setting values.
    $result = $action->create('Test', [123], [456], ['internal_domain_prefix' => 'xyz']);
    $expected = [
      'name' => 'Test',
      'site_ids' => [123],
      'group_ids' => [456],
      'internal_domain_prefix' => 'xyz',
    ];
    $this->assertEquals($expected, $result['json']);

  }

}
