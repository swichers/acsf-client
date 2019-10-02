<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Symfony helper class to load ACSF Client services into a container.
 */
class ServiceLoader {

  /**
   * Get a container with ACSF client services.
   *
   * @param string|NULL $servicePath
   *   The path to scan for the services file.
   * @param string $serviceFile
   *   The name of the services file.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerBuilder
   *   A container with the discovered services.
   *
   * @throws \Exception
   */
  public static function build(
    string $servicePath = NULL,
    string $serviceFile = 'services.yml'
  ): ContainerBuilder {

    static $containerBuilder;

    // Default to the client library configuration.
    if (empty($servicePath)) {
      $servicePath = __DIR__ . '/../';
    }

    $servicePath = realpath($servicePath);

    if (is_null($containerBuilder[$servicePath][$serviceFile])) {
      // This is required for the automatic loading of Annotation enabled
      // classes.
      AnnotationRegistry::registerLoader('class_exists');

      $containerBuilder[$servicePath][$serviceFile] = new ContainerBuilder();

      $loader = new YamlFileLoader(
        $containerBuilder[$servicePath][$serviceFile],
        new FileLocator($servicePath)
      );
      $loader->load($serviceFile);

    }

    return $containerBuilder[$servicePath][$serviceFile];
  }

}
