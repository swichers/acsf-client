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

    $collections = new Collections($this->mockClient);
    $this->assertSharedListValidation('collections', $collections, 'list');
  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $collections = new Collections($this->mockClient);
    $this->assertSame('Collection', $collections->getEntityType());
  }

  /**
   * @covers ::create
   */
  public function testCreate() {

    $collections = new Collections($this->mockClient);

    // No options means no options
    $result = $collections->create('Test', [123], [456], []);
    $expected = [
      'internal_method' => 'collections',
      'name' => 'Test',
      'site_ids' => [123],
      'group_ids' => [456],
    ];
    $this->assertEquals($expected, $result);

    // We're pruning keys.
    $result = $collections->create('Test', [123], [456], [
      'random_str' => TRUE,
      'internal_domain_prefix' => 'xyz',
    ]);
    $this->assertArrayNotHasKey('random_test', $result);
    $this->assertEquals('xyz', $result['internal_domain_prefix']);

    // We're setting values.
    $result = $collections->create('Test', [123], [456], ['internal_domain_prefix' => 'xyz']);
    $expected = [
      'internal_method' => 'collections',
      'name' => 'Test',
      'site_ids' => [123],
      'group_ids' => [456],
      'internal_domain_prefix' => 'xyz',
    ];
    $this->assertEquals($expected, $result);


  }

}
