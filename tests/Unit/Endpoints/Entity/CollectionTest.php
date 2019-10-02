<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Entity;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\Entity\Collection;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;
use swichers\Acsf\Client\Tests\Traits\AcsfClientTrait;

/**
 * Tests for the Collection entity type.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Entity\Collection
 *
 * @group AcsfClient
 */
class CollectionTest extends TestCase {

  use AcsfClientTrait;

  /**
   * Validate we can add a site to the Collection.
   *
   * @covers ::addSite
   */
  public function testAddSite() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->addSite([123, 456, 'abc', FALSE]);
    $this->assertEquals('collections/1234/add', $result['internal_method']);
    $this->assertEquals([123, 456], $result['json']['site_ids']);
  }

  /**
   * Validate that we get an exception when adding no sites.
   *
   * @covers ::addSite
   *
   * @depends testAddSite
   */
  public function testAddSiteNoSites() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $entity->addSite([]);
  }

  /**
   * Validate we can remove a site from a Collection.
   *
   * @covers ::removeSite
   */
  public function testRemoveSite() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->removeSite([123, 456, 'abc', FALSE]);
    $this->assertEquals('collections/1234/remove', $result['internal_method']);
    $this->assertEquals([123, 456], $result['json']['site_ids']);
  }

  /**
   * Validate we get an exception when removing no sites from a Collection.
   *
   * @covers ::removeSite
   *
   * @depends testRemoveSite
   */
  public function testRemoveSiteNoSites() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);

    $this->expectException(InvalidOptionException::class);
    $entity->removeSite([]);
  }

  /**
   * Validate we can get Collection details.
   *
   * @covers ::details
   */
  public function testDetails() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->details();
    $this->assertEquals('collections/1234', $result['internal_method']);
  }

  /**
   * Validate we can delete a Collection.
   *
   * @covers ::delete
   */
  public function testDelete() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $this->assertEquals(
      'collections/1234',
      $entity->delete()['internal_method']
    );
  }

  /**
   * Validate we can set the primary site of a Collection.
   *
   * @covers ::setPrimarySite
   */
  public function testSetPrimarySite() {

    $entity = new Collection($this->getMockAcsfClient(), 1234);
    $result = $entity->setPrimarySite(123);
    $this->assertEquals(
      'collections/1234/set-primary',
      $result['internal_method']
    );
    $this->assertEquals(123, $result['json']['site_id']);
  }

}
