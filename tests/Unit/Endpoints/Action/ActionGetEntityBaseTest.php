<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Endpoints\Action\ActionGetEntityBase;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\MissingEntityException;

/**
 * Class ActionGetEntityBaseTest
 *
 * @package swichers\Acsf\Tests\Client\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\ActionGetEntityBase
 */
class ActionGetEntityBaseTest extends TestCase {

  protected $mockClient;

  protected $mockBase;

  /**
   * @covers ::get
   */
  public function testGetSuccess() {

    $this->assertInstanceOf(EntityInterface::class, $this->mockBase->get(123));
    $this->assertInstanceOf(EntityInterface::class, $this->mockBase->get(456));
  }

  /**
   * @covers ::get
   */
  public function testGetFail() {

    $this->expectException(MissingEntityException::class);
    $this->assertInstanceOf(EntityInterface::class, $this->mockBase->get(789));
  }

  protected function setUp() {

    parent::setUp();

    $mockEntity = $this->getMockBuilder(EntityInterface::class)->getMock();
    $mockClient = $this->getMockBuilder(Client::class)
      ->disableOriginalConstructor()
      ->getMock();
    $mockClient->method('getEntity')->willReturnMap([
      ['Site', 123, $mockEntity],
      ['Task', 456, $mockEntity],
    ]);
    $mockClient->method('getEntity')
      ->willReturnCallback(function ($entityType, $entityId) use ($mockEntity) {

        $is_site = 'Site' == $entityType && 123 == $entityId;
        $is_task = 'Task' == $entityType && 456 == $entityId;
        if ($is_site || $is_task) {
          return $mockEntity;
        }
        throw new MissingEntityException();
      });

    $this->mockClient = $mockClient;

    /** @var \PHPUnit\Framework\MockObject\MockObject | \swichers\Acsf\Client\Endpoints\Action\ActionGetEntityBase $mock */
    $this->mockBase = $this->getMockBuilder(ActionGetEntityBase::class)
      ->setConstructorArgs([$this->mockClient])
      ->setMethods(['getEntityType'])
      ->getMockForAbstractClass();

    $this->mockBase->method('getEntityType')->willReturn('Site', 'Task');
  }

}
