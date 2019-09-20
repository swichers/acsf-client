<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Endpoints\Action\ActionBase;

/**
 * Class ActionBaseTest
 *
 * @package swichers\Acsf\Tests\Client\Endpoints\Action
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\ActionBase
 */
class ActionBaseTest extends TestCase {

  protected $mockClient;

  protected $mockBase;

  /**
   * @covers ::__construct
   */
  public function test__construct() {

    /** @var \PHPUnit\Framework\MockObject\MockObject | \swichers\Acsf\Client\Endpoints\Action\ActionGetEntityBase $mock */
    $mock = $this->getMockBuilder(ActionBase::class)
      ->setConstructorArgs([$this->mockClient])
      ->getMockForAbstractClass();

    $reflectionClass = new ReflectionClass(ActionBase::class);
    $reflectionProperty = $reflectionClass->getProperty('client');
    $reflectionProperty->setAccessible(TRUE);

    $this->assertInstanceOf(Client::class, $reflectionProperty->getValue($mock));
  }

  protected function setUp() {

    parent::setUp();

    $mockClient = $this->getMockBuilder(Client::class)
      ->disableOriginalConstructor()
      ->getMock();
    $this->mockClient = $mockClient;
  }

}
