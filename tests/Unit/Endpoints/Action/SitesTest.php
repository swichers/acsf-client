<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\Sites;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Endpoints\Entity\Site;
use swichers\Acsf\Client\Exceptions\MissingEntityException;

/**
 * Tests for the SitesTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\Sites
 *
 * @group AcsfClient
 */
class SitesTest extends AbstractActionTestBase {

  /**
   * Validate we can get a list of Sites.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new Sites($this->getMockAcsfClient());
    $this->assertSharedListValidation('sites', $action, 'list');

    $options = [
      'canary' => 'yes',
      'show_incomplete' => 'yes',
    ];
    $result = $action->list($options);
    $this->assertTrue($result['query']['canary']);
    $this->assertTrue($result['query']['show_incomplete']);
    $options = [
      'canary' => 'no',
      'show_incomplete' => 'no',
    ];
    $result = $action->list($options);
    $this->assertFalse($result['query']['canary']);
    $this->assertFalse($result['query']['show_incomplete']);
  }

  /**
   * Validate we can create new Sites.
   *
   * @covers ::create
   */
  public function testCreate() {

    $action = new Sites($this->getMockAcsfClient());
    $options = [
      'stack_id' => -1,
      'group_ids' => [123, '456', 'abc'],
      'install_profile' => 'unmodified',
    ];
    $result = $action->create('UnitTest', $options);
    $this->assertEquals('sites', $result['internal_method']);
    $this->assertEquals('unmodified', $result['json']['install_profile']);
    $this->assertEquals('UnitTest', $result['json']['site_name']);
    $this->assertEquals(1, $result['json']['stack_id']);
    $this->assertEquals([123, 456], $result['json']['group_ids']);

    $result = $action->create('UnitTest', ['stack_id' => 20]);
    $this->assertEquals(20, $result['json']['stack_id']);
  }

  /**
   * Validate we can get the Site entity type.
   *
   * @covers ::getEntityType
   */
  public function testGetEntityType() {

    $action = new Sites($this->mockClient);
    $this->assertSame('Site', $action->getEntityType());
  }

  /**
   * Validates we can get a list of all Sites despite pagination.
   *
   * @covers ::listAll
   */
  public function testListAll() {

    /** @var \swichers\Acsf\Client\Endpoints\Action\Sites|\PHPUnit\Framework\MockObject\MockObject $action */
    $action = $this->getMockBuilder(Sites::class)
      ->setConstructorArgs([$this->mockClient])
      ->onlyMethods(['list'])
      ->getMock();
    $action->method('list')->willReturnCallback(function ($options) {
      $page = $options['page'] ?: 1;

      $sites = [];

      if ($page == 1) {
        $sites = range(1, 10);
      }
      elseif ($page == 2) {
        $sites = range(1, 5);
      }

      return ['sites' => $sites, 'count' => 15];
    });

    $list = $action->listAll();
    $this->assertArrayHasKey('sites', $list);
    $this->assertArrayHasKey('count', $list);
    $this->assertCount(15, $list['sites']);
    $this->assertEquals(15, $list['count']);
  }

  /**
   * Validates we can get a Site by its name.
   *
   * @covers ::getByName
   */
  public function testGetByName() {

    /** @var \swichers\Acsf\Client\Endpoints\Action\Sites|\PHPUnit\Framework\MockObject\MockObject $action */
    $action = $this->getMockBuilder(Sites::class)
      ->setConstructorArgs([$this->mockClient])
      ->onlyMethods(['listAll', 'get'])
      ->getMock();

    $action->method('get')->willReturnMap([
      [123, new Site($this->mockClient, 123)],
    ]);

    $action->method('listAll')->willReturn([
      'sites' => [
        [
          'site' => 'UnitTest',
          'id' => 123,
        ],
      ],
    ]);

    /** @var \swichers\Acsf\Client\Endpoints\Entity\Site $site */
    $site = $action->getByName('UnitTest');
    $this->assertInstanceOf(EntityInterface::class, $site);
    $this->assertEquals(123, $site->id());
  }

  /**
   * Validate we get a MissingEntityException if a bad name is given.
   *
   * @covers ::getByName
   *
   * @depends testGetByName
   */
  public function testGetByNameMissing() {

    $action = new Sites($this->getMockAcsfClient());
    $this->expectException(MissingEntityException::class);
    $action->getByName('No site');
  }

}
