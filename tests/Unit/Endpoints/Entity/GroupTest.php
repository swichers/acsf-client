<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Group;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Group entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Group
 *
 * @group AcsfClient
 */
class GroupTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can get Group details.
   *
   * @covers ::details
   */
  public function testDetails() {

    $entity = new Group($this->getMockAcsfClient(), 1234);
    $result = $entity->details();
    $this->assertEquals('groups/1234', $result['internal_method']);
    $this->assertEmpty($result['query']);
  }

  /**
   * Validate we can get a list of Group members.
   *
   * @covers ::members
   */
  public function testMembers() {

    $entity = new Group($this->getMockAcsfClient(), 1234);
    $options = [
      'random' => TRUE,
      'page' => -1,
      'limit' => 300,
    ];
    $result = $entity->members($options);
    $this->assertEquals('groups/1234/members', $result['internal_method']);
    $this->assertEquals(1, $result['query']['page']);
    $this->assertEquals(100, $result['query']['limit']);
    $this->assertArrayNotHasKey('random', $result['query']);

    $result = $entity->members(['page' => 2, 'limit' => 10]);
    $this->assertEquals(2, $result['query']['page']);
    $this->assertEquals(10, $result['query']['limit']);
  }

}
