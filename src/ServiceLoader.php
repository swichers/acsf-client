<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Symfony helper class to load ACSF Client services into a container.
 *
 * @deprecated This class has been superseded by ClientFactory.
 *
 * @see \swichers\Acsf\Client\ClientFactory
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
   *
   * @deprecated Replaced by the getServices method in ClientFactory.
   *
   * @see \swichers\Acsf\Client\ClientFactory::getServices()
   */
  public static function build(string $servicePath = NULL, string $serviceFile = 'services.yml'): ContainerBuilder {

    return ClientFactory::getServices($servicePath, $serviceFile);
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
   * @deprecated Replaced by createFromConfig in ClientFactory
   *
   * @see \swichers\Acsf\Client\ClientFactory::createFromConfig()
   */
  public static function buildFromConfig(array $config, string $servicePath = NULL, string $serviceFile = 'services.yml'): ContainerBuilder {

    return ClientFactory::getServices($servicePath, $serviceFile, $config);
  }

}
