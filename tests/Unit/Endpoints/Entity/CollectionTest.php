<?php

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Collection;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Class CollectionTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints\Entity
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Collection
 */
class CollectionTest extends TestCase {

  use AcsfClientTrait;

  /**
   * @covers ::addSite
   */
  public function testAddSite() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->addSite([123, 456, 'abc', FALSE]);
    $this->assertEquals('collections/1234/add', $result['internal_method']);
    $this->assertEquals([123, 456], $result['json']['site_ids']);
  }

  /**
   * @covers ::addSite
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testAddSiteNoSites() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $entity->addSite([]);
  }

  /**
   * @covers ::removeSite
   */
  public function testRemoveSite() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->removeSite([123, 456, 'abc', FALSE]);
    $this->assertEquals('collections/1234/remove', $result['internal_method']);
    $this->assertEquals([123, 456], $result['json']['site_ids']);
  }

  /**
   * @covers ::removeSite
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRemoveSiteNoSites() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $entity->removeSite([]);
  }

  /**
   * @covers ::details
   */
  public function testDetails() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->details();
    $this->assertEquals('collections/1234', $result['internal_method']);

  }

  /**
   * @covers ::delete
   */
  public function testDelete() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $this->assertEquals('collections/1234', $entity->delete()['internal_method']);
  }

  /**
   * @covers ::setPrimarySite
   */
  public function testSetPrimarySite() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->setPrimarySite(123);
    $this->assertEquals('collections/1234/set-primary', $result['internal_method']);
    $this->assertEquals(123, $result['json']['site_id']);
  }

}
