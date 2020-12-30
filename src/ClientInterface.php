<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

/**
 * Contract for Clients.
 */
interface ClientInterface {

  /**
   * Get the base URL for an API endpoint.
   *
   * @param int $version
   *   The version of the API endpoint to request.
   *
   * @return string
   *   A base API URL to use.
   */
  public function getApiUrl(int $version = 1): string;

  /**
   * Get an Action endpoint helper.
   *
   * @param string $type
   *   The Action name.
   *
   * @return \swichers\Acsf\Client\Endpoints\Action\ActionInterface
   *   The requested Action.
   */
  public function getAction(string $type): ActionInterface;

  /**
   * Make a GET request to the API.
   *
   * @param string|array $method
   *   The API method to call.
   * @param array $params
   *   An array of options to pass.
   * @param int|null $api_version
   *   The version of the API to use. Defaults to 1.
   *
   * @return \swichers\Acsf\Client\ResponseInterface
   *   The API response.
   */
  public function apiGet($method, array $params = [], int $api_version = NULL): ResponseInterface;

  /**
   * Make a PUT request to the API.
   *
   * @param string|array $method
   *   The API method to call.
   * @param array $data
   *   An array of data to pass.
   * @param int|null $api_version
   *   The version of the API to use. Defaults to 1.
   *
   * @return \swichers\Acsf\Client\ResponseInterface
   *   The API response.
   */
  public function apiPut($method, array $data, int $api_version = NULL): ResponseInterface;

  /**
   * Make a POST request to the API.
   *
   * @param string|array $method
   *   The API method to call.
   * @param array $data
   *   An array of data to pass.
   * @param int|null $api_version
   *   The version of the API to use. Defaults to 1.
   *
   * @return \swichers\Acsf\Client\ResponseInterface
   *   The API response.
   */
  public function apiPost($method, array $data, int $api_version = NULL): ResponseInterface;

  /**
   * Make a DELETE request to the API.
   *
   * @param string|array $method
   *   The API method to call.
   * @param array $data
   *   An array of data to pass.
   * @param int|null $api_version
   *   The version of the API to use. Defaults to 1.
   *
   * @return \swichers\Acsf\Client\ResponseInterface
   *   The API response.
   */
  public function apiDelete($method, array $data, int $api_version = NULL): ResponseInterface;

  /**
   * Get an Entity endpoint helper.
   *
   * @param string $type
   *   The type of entity to get.
   * @param int $entityId
   *   The ID of the entity to get.
   * @param \swichers\Acsf\Client\Endpoints\Entity\EntityInterface $parent
   *   The parent entity (if any).
   *
   * @return \swichers\Acsf\Client\Endpoints\Entity\EntityInterface
   *   The requested Entity.
   */
  public function getEntity(string $type, int $entityId, EntityInterface $parent = NULL): EntityInterface;

  /**
   * Test the ACSF Client connection.
   *
   * @param bool $throwException
   *   TRUE to throw an exception on failure.
   *
   * @return bool
   *   TRUE if the connection was successful, FALSE otherwise.
   */
  public function testConnection($throwException = FALSE): bool;

  /**
   * Set new client configuration.
   *
   * @param array $config
   *   The new configuration to set.
   *
   * @return array
   *   The previous client configuration.
   */
  public function setConfig(array $config): array;

  /**
   * Get the current client configuration.
   *
   * @return array
   *   The current client configuration.
   */
  public function getConfig(): array;

  /**
   * Set the active environment.
   *
   * @param string $environment
   *   The environment to change to.
   *
   * @return string
   *   The previous environment.
   */
  public function setEnvironment(string $environment): string;

  /**
   * Get the active environment.
   *
   * @return string
   *   The active environment.
   */
  public function getEnvironment(): string;

}
