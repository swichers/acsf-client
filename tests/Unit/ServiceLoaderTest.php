<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests;

use swichers\Acsf\Client\ServiceLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ServiceLoaderTest.
 *
 * @coversDefaultClass \swichers\Acsf\Client\ServiceLoader
 *
 * @group AcsfClient
 */
class ServiceLoaderTest extends TestCase {

  /**
   * Validate our service loader functions.
   *
   * @covers ::build
   *
   * @TODO: This test sucks.
   */
  public function testBuild() {

    $loader = ServiceLoader::build();
    $this->assertInstanceOf(ContainerBuilder::class, $loader);
    $this->assertInstanceOf(HttpClientInterface::class, $loader->get('acsf.http_client'));
  }

}
