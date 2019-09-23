<?php

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
 * Class ManagerTest
 *
 * @package swichers\Acsf\Tests\Discovery
 *
 * @coversDefaultClass \swichers\Acsf\Client\Discovery\Manager
 */
class ManagerTest extends TestCase {

  protected $actionDiscoverer;

  /**
   * @covers ::getAvailable
   * @covers ::__construct
   */
  public function testGetAvailable() {

    $manager = new Manager($this->actionDiscoverer);
    $this->assertArrayHasKey('Sites', $manager->getAvailable());
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  public function testCreate() {

    $manager = new Manager($this->actionDiscoverer);

    $mockClient = $this->getMockBuilder(Client::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->assertInstanceOf(Sites::class, $manager->create('Sites', $mockClient, 123));
  }

  /**
   * @covers ::create
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\MissingEndpointException
   */
  public function testCreateFailType() {

    $manager = new Manager($this->actionDiscoverer);
    $manager->create('Abc' . time());
  }

  /**
   * @covers ::get
   * @covers ::__construct
   */
  public function testGet() {

    $manager = new Manager($this->actionDiscoverer);
    $this->assertInternalType('array', $manager->get('Sites'));
    $this->assertFalse($manager->get('Abc' . time()));
  }

  protected function setUp() {

    parent::setUp();

    AnnotationRegistry::registerLoader('class_exists');

    $namespace = '\\swichers\\Acsf\\Client\\Endpoints\\Action';
    $directory = 'Endpoints/Action';
    $rootDir = '.';
    $annotationClass = Action::class;
    $annotationReader = new AnnotationReader();

    $this->actionDiscoverer = new Discoverer($namespace, $directory, $rootDir, $annotationClass, $annotationReader);
  }
}
