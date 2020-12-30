<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Discovery;

use swichers\Acsf\Client\Endpoints\EndpointInterface;

/**
 * Contract for managers.
 */
interface ManagerInterface {

  /**
   * Get the discovered items.
   *
   * @return array
   *   An array of discovered items.
   */
  public function getAvailable(): array;

  /**
   * Get details about the discovered class.
   *
   * @param string $name
   *   The name of a discovered Endpoint.
   *
   * @return bool|object
   *   The Annotation object or FALSE if not found.
   */
  public function get(string $name);

  /**
   * Create an instance of the requested class.
   *
   * @param string $name
   *   The name of the item type to create.
   * @param mixed $constructor_args
   *   Arguments vary based on the type of item being created.
   *
   * @return \swichers\Acsf\Client\Endpoints\EndpointInterface
   *   The created object.
   */
  public function create(string $name, ...$constructor_args): EndpointInterface;

}
