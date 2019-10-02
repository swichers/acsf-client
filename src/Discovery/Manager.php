<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Discovery;

use swichers\Acsf\Client\Endpoints\EndpointInterface;
use swichers\Acsf\Client\Exceptions\MissingEndpointException;

/**
 * Class Manager.
 */
class Manager implements ManagerInterface {

  /**
   * A typed Endpoint discoverer.
   *
   * @var \swichers\Acsf\Client\Discovery\Discoverer
   */
  protected $discovery;

  /**
   * Manager constructor.
   *
   * @param \swichers\Acsf\Client\Discovery\DiscovererInterface $discovery
   *   The API Endpoint discoverer.
   */
  public function __construct(DiscovererInterface $discovery) {

    $this->discovery = $discovery;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \ReflectionException
   * @throws \swichers\Acsf\Client\Exceptions\MissingEndpointException
   */
  public function create(string $name, ...$constructor_args): EndpointInterface {

    $workers = $this->discovery->getItems();
    if (array_key_exists($name, $workers)) {
      $class = $workers[$name]['class'];
      if (!class_exists($class)) {
        throw new MissingEndpointException(
          'Implementation class does not exist.'
        );
      }

      return new $class(...$constructor_args);
    }

    throw new MissingEndpointException('Implementation does not exist.');
  }

  /**
   * {@inheritdoc}
   */
  public function get(string $name) {

    $workers = $this->discovery->getItems();
    if (isset($workers[$name])) {
      return $workers[$name];
    }

    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function getAvailable(): array {

    return $this->discovery->getItems();
  }

}
