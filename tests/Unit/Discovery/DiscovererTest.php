<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Discovery;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Discovery\Discoverer;
use swichers\Acsf\Client\Endpoints\Action\Sites;

/**
 * Tests for our Action and Entity discoverer helper.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Discovery\Discoverer
 *
 * @group AcsfClient
 */
class DiscovererTest extends TestCase {

  /**
   * Validate we can get a list of discovered items.
   *
   * @covers ::getItems
   * @covers ::discoverItems
   * @covers ::__construct
   */
  public function testGetItems() {

    // We must register a default loader of class_exists.
    AnnotationRegistry::registerLoader('class_exists');

    $namespace = '\\swichers\\Acsf\\Client\\Endpoints\\Action';
    $disc = new Discoverer(
      $namespace, 'Endpoints/Action', '.', Action::class, new AnnotationReader()
    );
    $items = $disc->getItems();

    $this->assertArrayHasKey('Sites', $items);

    $action = new Action();
    $action->name = 'Sites';
    $action->entityType = 'Site';

    $sites = [
      'class' => '\\' . Sites::class,
      'annotation' => $action,
    ];
    $this->assertEquals($sites, $items['Sites']);
  }

}
