<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Endpoints\Action\AbstractEntityAction;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\MissingEntityException;

/**
 * Tests for AbstractEntityAction.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\AbstractEntityAction
 *
 * @group AcsfClient
 */
class AbstractEntityActionTest extends TestCase {

  /**
   * A mock ACSF Client.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\ClientInterface
   */
  protected $mockClient;

  /**
   * A mock class implementing ActionGetEntityBase.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Endpoints\Action\AbstractEntityAction
   */
  protected $mockBase;

  /**
   * Validate we can get an Entity by ID.
   *
   * @covers ::get
   */
  public function testGetSuccess() {

    $this->assertInstanceOf(EntityInterface::class, $this->mockBase->get(123));
    $this->assertInstanceOf(EntityInterface::class, $this->mockBase->get(456));
  }

  /**
   * Validate we get an exception if an Entity does not exit.
   *
   * @covers ::get
   *
   * @depends testGetSuccess
   */
  public function testGetFail() {

    $this->expectException(MissingEntityException::class);
    $this->mockBase->get(789);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {

    parent::setUp();

    $mockEntity = $this->getMockBuilder(EntityInterface::class)->getMock();
    $mockClient =
      $this->getMockBuilder(Client::class)
        ->disableOriginalConstructor()
        ->getMock();
    $mockClient->method('getEntity')->willReturnCallback(
      function ($entityType, $entityId) use ($mockEntity) {

        $is_site = 'Site' == $entityType && 123 == $entityId;
        $is_task = 'Task' == $entityType && 456 == $entityId;
        if ($is_site || $is_task) {
          return $mockEntity;
        }
        throw new MissingEntityException();
      }
    );

    $this->mockClient = $mockClient;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Endpoints\Action\AbstractEntityAction $mock */
    $this->mockBase =
      $this->getMockBuilder(AbstractEntityAction::class)->setConstructorArgs(
          [$this->mockClient]
        )->setMethods(['getEntityType'])->getMockForAbstractClass();

    $this->mockBase->method('getEntityType')->willReturn('Site', 'Task');
  }

}
