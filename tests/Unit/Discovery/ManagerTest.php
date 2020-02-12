<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Discovery;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Discovery\Discoverer;
use swichers\Acsf\Client\Discovery\Manager;
use swichers\Acsf\Client\Endpoints\Action\Sites;
use swichers\Acsf\Client\Exceptions\MissingEndpointException;

/**
 * Tests for our Action and Entity managers.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Discovery\Manager
 *
 * @group AcsfClient
 */
class ManagerTest extends TestCase {

  /**
   * An Action discoverer.
   *
   * @var \swichers\Acsf\Client\Discovery\Discoverer
   */
  protected $actionDiscoverer;

  /**
   * Validates our manager can return discovered Actions.
   *
   * @covers ::getAvailable
   * @covers ::__construct
   */
  public function testGetAvailable() {

    $manager = new Manager($this->actionDiscoverer);
    $this->assertArrayHasKey('Sites', $manager->getAvailable());
  }

  /**
   * Validates we can create a Manager of a certain type.
   *
   * @covers ::create
   * @covers ::__construct
   */
  public function testCreate() {

    $manager = new Manager($this->actionDiscoverer);

    $mockClient =
      $this->getMockBuilder(Client::class)
        ->disableOriginalConstructor()
        ->getMock();

    $this->assertInstanceOf(
      Sites::class,
      $manager->create('Sites', $mockClient, 123)
    );
  }

  /**
   * Validate we get an exception when trying to create an invalid Manager.
   *
   * @covers ::create
   *
   * @depends testCreate
   */
  public function testCreateFailType() {

    $manager = new Manager($this->actionDiscoverer);

    $this->expectException(MissingEndpointException::class);
    $manager->create('Abc' . time());
  }

  /**
   * Validate we can retrieve an Action from the Manager.
   *
   * @covers ::get
   * @covers ::__construct
   */
  public function testGet() {

    $manager = new Manager($this->actionDiscoverer);
    $this->assertIsArray($manager->get('Sites'));
    $this->assertFalse($manager->get('Abc' . time()));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {

    parent::setUp();

    // We must register a default loader of class_exists.
    AnnotationRegistry::registerLoader('class_exists');

    $namespace = '\\swichers\\Acsf\\Client\\Endpoints\\Action';
    $this->actionDiscoverer = new Discoverer(
      $namespace, 'Endpoints/Action', '.', Action::class, new AnnotationReader()
    );
  }

}
