<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Collections;

/**
 * Tests for the CollectionsTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Collections
 *
 * @group AcsfClient
 */
class CollectionsTest extends AbstractActionTestBase {

  /**
   * Validate we can list Collections.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Collections($this->mockClient);
    $this->assertSharedListValidation('collections', $action, 'list');
  }

  /**
   * Validate we get a Collection entity type.
   *
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Collections($this->mockClient);
    $this->assertSame('Collection', $action->getEntityType());
  }

  /**
   * Validate we can create a Collection.
   *
   * @covers ::create
   */
  public function testCreate() {

    $action = new Collections($this->mockClient);

    $result = $action->create('Test', [123], [456], []);
    $this->assertEquals('collections', $result['internal_method']);

    // No options means no options.
    $expected = [
      'name' => 'Test',
      'site_ids' => [123],
      'group_ids' => [456],
    ];
    $this->assertEquals($expected, $result['json']);

    // We're pruning keys.
    $result = $action->create(
      'Test',
      [123],
      [456],
      [
        'random_str' => TRUE,
        'internal_domain_prefix' => 'xyz',
      ]
    );
    $this->assertArrayNotHasKey('random_test', $result['json']);
    $this->assertEquals('xyz', $result['json']['internal_domain_prefix']);

    // We're setting values.
    $result = $action->create(
      'Test',
      [123],
      [456],
      ['internal_domain_prefix' => 'xyz']
    );
    $expected = [
      'name' => 'Test',
      'site_ids' => [123],
      'group_ids' => [456],
      'internal_domain_prefix' => 'xyz',
    ];
    $this->assertEquals($expected, $result['json']);
  }

}
