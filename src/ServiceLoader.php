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
   * @param string|null $servicePath
   *   The path to scan for the services file.
   * @param string $serviceFile
   *   The name of the services file.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerBuilder
   *   A container with the discovered services.
   *
   * @BUG Can't get unique clients via the service container.
   *   Do we want this behavior? It's clunky to get a workable client using the
   *   container, but easy to change the config after we have it.
   *   https://symfony.com/doc/current/service_container/shared.html
   */
  public static function build(string $servicePath = NULL, string $serviceFile = 'services.yml'): ContainerBuilder {

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

  /**
   * Creates a service container and sets parameters.
   *
   * @param array $config
   *   A key value array of configuration to set on the container.
   * @param string|null $servicePath
   *   The path to scan for the services file.
   * @param string $serviceFile
   *   The name of the services file.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerBuilder
   *   A container with the discovered services.
   *
   * @see \swichers\Acsf\Client\ServiceLoader::build()
   */
  public static function buildFromConfig(array $config, string $servicePath = NULL, string $serviceFile = 'services.yml'): ContainerBuilder {
    $container = self::build($servicePath, $serviceFile);

    foreach ($config as $key => $value) {
      $container->setParameter($key, $value);
    }

    return $container;
  }

}
