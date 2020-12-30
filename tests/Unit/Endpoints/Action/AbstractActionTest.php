<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Endpoints\Action\AbstractAction;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the AbstractAction class.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\AbstractAction
 *
 * @group AcsfClient
 */
class AbstractActionTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validates the constructor sets the client properly.
   *
   * @covers ::__construct
   */
  public function testConstructor() {

    /** @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Endpoints\Action\AbstractEntityAction $mock */
    $mock = $this->getMockBuilder(AbstractAction::class)->setConstructorArgs(
      [$this->getMockAcsfClient()]
    )->getMockForAbstractClass();

    $reflectionClass = new \ReflectionClass(AbstractAction::class);
    $reflectionProperty = $reflectionClass->getProperty('client');
    $reflectionProperty->setAccessible(TRUE);

    self::assertInstanceOf(
      Client::class,
      $reflectionProperty->getValue($mock)
    );
  }

}
