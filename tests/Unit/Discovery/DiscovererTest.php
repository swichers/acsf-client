<?php

namespace swichers\Acsf\Client\Tests\Discovery;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Discovery\Discoverer;
use swichers\Acsf\Client\Endpoints\Action\Sites;

/**
 * Class DiscovererTest
 *
 * @package swichers\Acsf\Tests\Discovery
 *
 * @coversDefaultClass \swichers\Acsf\Client\Discovery\Discoverer
 */
class DiscovererTest extends TestCase {

  /**
   * @covers ::getItems
   * @covers ::discoverItems
   * @covers ::__construct
   */
  public function testGetItems() {

    AnnotationRegistry::registerLoader('class_exists');

    $namespace = '\\swichers\\Acsf\\Client\\Endpoints\\Action';
    $directory = 'Endpoints/Action';
    $rootDir = '.';
    $annotationClass = Action::class;
    $annotationReader = new AnnotationReader();

    $disc = new Discoverer($namespace, $directory, $rootDir, $annotationClass, $annotationReader);
    $items = $disc->getItems();

    $this->assertArrayHasKey('Sites', $items);

    $action = new Action();
    $action->name = 'Sites';
    $action->entity_type = 'Site';

    $sites = [
      'class' => '\\' . Sites::class,
      'annotation' => $action,
    ];
    $this->assertEquals($sites, $items['Sites']);
  }
}
