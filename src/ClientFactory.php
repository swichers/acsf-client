<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\PathException;

/**
 * Factory for creating ACSF Clients through a variety of methods.
 */
class ClientFactory {

  /**
   * Create a Client by using an array of configuration.
   *
   * @param array $config
   *   A key value array containing username, api_key, site_group, and
   *   environment values.
   *
   * @return \swichers\Acsf\Client\ClientInterface
   *   An initialized client.
   */
  public static function createFromArray(array $config): ClientInterface {

    return self::createFromConfig(['acsf.client.connection' => $config]);
  }

  /**
   * Create a Client by using environment variables.
   *
   * Looks for the following environment variables and uses them to create a
   * client:
   *
   * ACSF_API_USERNAME
   * ACSF_API_KEY
   * AH_SITE_GROUP
   * AH_SITE_ENVIRONMENT
   *
   * The passed in environment value will be preferred over ACSF_ENVIRONMENT.
   *
   * @param string|null $environment
   *   The ACSF environment to target. Defaults to dev.
   * @param bool|true $loadEnvFile
   *   If true, attempt to load a .env file and use its values.
   *
   * @return \swichers\Acsf\Client\ClientInterface
   *   An initialized client.
   *
   * @see \swichers\Acsf\Client\ClientFactory::getEnvPaths()
   */
  public static function createFromEnvironment(string $environment = NULL, bool $loadEnvFile = TRUE): ClientInterface {

    if ($loadEnvFile) {
      foreach (self::getEnvPaths() as $path) {
        try {
          (new Dotenv())->load($path);
        }
        catch (PathException $x) {
          // We don't necessarily care if this fails. It just means we rely on
          // the actual system environment variables.
        }
      }
    }

    // Prefer what was passed in, fall back to the environment defined target,
    // and then finally fall back to dev.
    $environment =
      ($environment ?: (string) getenv('AH_SITE_ENVIRONMENT')) ?: 'dev';

    return self::createFromArray(
      [
        'username' => getenv('ACSF_API_USERNAME'),
        'api_key' => getenv('ACSF_API_KEY'),
        'site_group' => getenv('AH_SITE_GROUP'),
        'environment' => $environment,
      ]
    );
  }

  /**
   * Create a client using values directly.
   *
   * @param string $username
   *   The username to use.
   * @param string $key
   *   The API key to use.
   * @param string $siteGroup
   *   The ACSF site group.
   * @param string $environment
   *   The ACSF environment. i.e. 01dev.
   *
   * @return \swichers\Acsf\Client\ClientInterface
   *   An initialized client.
   */
  public static function create(string $username, string $key, string $siteGroup, string $environment = 'dev'): ClientInterface {

    return self::createFromArray(
      [
        'username' => $username,
        'api_key' => $key,
        'site_group' => $siteGroup,
        'environment' => $environment ?: 'dev',
      ]
    );
  }

  /**
   * Get a container with ACSF client services.
   *
   * @param string|null $servicePath
   *   The path to scan for the services file.
   * @param string $serviceFile
   *   The name of the services file.
   * @param array|null $config
   *   A key value array of configuration to set on the container. The client
   *   uses acsf.client.connection for its configuration.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerBuilder
   *   A container with the discovered services.
   *
   * @BUG Can't get unique clients via the service container.
   *   Do we want this behavior? It's clunky to get a workable client using the
   *   container, but easy to change the config after we have it.
   *   https://symfony.com/doc/current/service_container/shared.html
   */
  public static function getServices(string $servicePath = NULL, string $serviceFile = 'services.yml', array $config = []): ContainerBuilder {

    static $containerBuilder;

    // Default to the client library configuration.
    if (empty($servicePath)) {
      $servicePath = __DIR__ . '/../';
    }

    $servicePath = realpath($servicePath);

    if (empty($containerBuilder[$servicePath][$serviceFile])) {
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

    if (!empty($config)) {
      foreach ($config as $key => $value) {
        $containerBuilder[$servicePath][$serviceFile]->setParameter(
          $key,
          $value
        );
      }
    }

    return $containerBuilder[$servicePath][$serviceFile];
  }

  /**
   * Create a client through a service container.
   *
   * @param array $config
   *   A key value array of client configuration.
   * @param string|null $servicePath
   *   The path to scan for the services file.
   * @param string $serviceFile
   *   The name of the services file.
   *
   * @return \swichers\Acsf\Client\ClientInterface
   *   An initialized client.
   *
   * @see \swichers\Acsf\Client\ClientFactory::createFromArray()
   * @see \swichers\Acsf\Client\ClientFactory::getServices()
   */
  public static function createFromConfig(array $config, string $servicePath = NULL, string $serviceFile = 'services.yml'): ClientInterface {

    $container = self::getServices(
      $servicePath,
      $serviceFile,
      $config
    );

    return $container->get('acsf.client');
  }

  /**
   * Get the list of paths to look for .env files in.
   *
   * @return string[]|array
   *   A list of paths to check for .env files.
   */
  protected static function getEnvPaths(): array {

    $paths = [
      getcwd(),
    ];

    // Add Acquia environment paths.
    if (!empty(getenv('AH_SITE_NAME'))) {
      $paths[] = vsprintf(
        '/mnt/gfs/%s.%s',
        [
          getenv('AH_SITE_GROUP'),
          getenv('AH_SITE_ENVIRONMENT'),
        ]
      );
    }

    array_walk(
      $paths,
      static function (&$item) {

        $item .= '/.env';
      }
    );

    return $paths;
  }

}
