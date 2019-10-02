<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ServiceLoader {

  public static function build(
    string $servicePath = NULL,
    string $serviceFile = 'services.yml'
  ): ContainerBuilder {

    static $containerBuilder;

    if (empty($servicePath)) {
      $servicePath = __DIR__ . '/../';
    }

    $servicePath = realpath($servicePath);

    if (is_null($containerBuilder[$servicePath][$serviceFile])) {
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
