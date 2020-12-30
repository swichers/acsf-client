<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use swichers\Acsf\Client\Discovery\ActionManagerInterface;
use swichers\Acsf\Client\Discovery\EntityManagerInterface;
use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\InvalidConfigurationException;
use swichers\Acsf\Client\Exceptions\InvalidCredentialsException;
use swichers\Acsf\Client\Exceptions\MissingActionException;
use swichers\Acsf\Client\Exceptions\MissingEntityException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * ACSF Client class.
 */
class Client implements ClientInterface {

  /**
   * A Symfony HTTP Client.
   *
   * @var \Symfony\Contracts\HttpClient\HttpClientInterface
   */
  protected $httpClient;

  /**
   * An Action Manager.
   *
   * @var \swichers\Acsf\Client\Discovery\ActionManagerInterface
   */
  protected $actionManager;

  /**
   * An Entity Manager.
   *
   * @var \swichers\Acsf\Client\Discovery\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The active client configuration.
   *
   * Expects an array of username, api_key, site_group, and environment.
   *
   * @var array
   */
  protected $config;

  /**
   * Client constructor.
   *
   * @param \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient
   *   A Symfony HTTP Client.
   * @param \swichers\Acsf\Client\Discovery\ActionManagerInterface $actionManager
   *   An Action Manager.
   * @param \swichers\Acsf\Client\Discovery\EntityManagerInterface $entityManager
   *   An Entity Manager.
   * @param array $config
   *   An array of client configuration. Expects an array of username, api_key,
   *   site_group, and environment.
   */
  public function __construct(HttpClientInterface $httpClient, ActionManagerInterface $actionManager, EntityManagerInterface $entityManager, array $config) {

    $this->httpClient = $httpClient;
    $this->actionManager = $actionManager;
    $this->entityManager = $entityManager;
    $this->setConfig($config);
    $this->testConnection(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function apiDelete($method, array $data, int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('DELETE', $method, ['json' => $data], $api_version);
  }

  /**
   * {@inheritdoc}
   */
  public function apiGet($method, array $params = [], int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('GET', $method, ['query' => $params], $api_version);
  }

  /**
   * {@inheritdoc}
   */
  public function apiPost($method, array $data, int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('POST', $method, ['json' => $data], $api_version);
  }

  /**
   * {@inheritdoc}
   */
  public function apiPut($method, array $data, int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('PUT', $method, ['json' => $data], $api_version);
  }

  /**
   * {@inheritdoc}
   */
  public function getAction(string $type): ActionInterface {

    if (!$this->actionManager->get($type)) {
      throw new MissingActionException(
        sprintf('Action %s was not registered with the client.', $type)
      );
    }

    return $this->actionManager->create($type, $this);
  }

  /**
   * {@inheritdoc}
   */
  public function getApiUrl(int $version = 1): string {

    $env_prefix =
      $this->config['environment'] == 'live' ? ''
        : $this->config['environment'] . '-';

    return sprintf(
      'https://www.%s%s.acsitefactory.com/api/v%d/',
      $env_prefix,
      $this->config['site_group'],
      max($version, 1)
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(): array {

    return $this->config ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfig(array $config): array {

    $this->validateConfig($config);
    $old_config = $this->config ?? [];
    $this->config = $config;
    $this->config['site_group'] = strtolower($this->config['site_group'] ?? '');
    $this->setEnvironment($config['environment'] ?? '');

    return $old_config;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnvironment(string $environment): string {
    $current_environment = $this->getEnvironment();

    // AH_SITE_ENVIRONMENT can have the stack ID in it, so let's blindly strip
    // starting numbers from our given environment. This should be safe because
    // valid environment names cannot start with numbers anyway.
    $environment = (string) preg_replace('/^\d/m', '', $environment);
    $this->config['environment'] = strtolower($environment);

    return $current_environment;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnvironment(): string {
    return $this->config['environment'];
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity(string $type, int $entityId, EntityInterface $parent = NULL): EntityInterface {

    if (!$this->entityManager->get($type)) {
      throw new MissingEntityException(
        sprintf('Entity %s was not registered with the client.', $type)
      );
    }

    return $this->entityManager->create($type, $this, $entityId, $parent);
  }

  /**
   * {@inheritdoc}
   */
  public function testConnection($throwException = FALSE): bool {

    try {
      /** @var \swichers\Acsf\Client\Endpoints\Action\Status $status */
      $status = $this->getAction('Status');
      $status->ping();

      return TRUE;
    }
    catch (ClientException $x) {
      if ($throwException) {
        if ($x->getCode() === 403) {
          throw new InvalidCredentialsException(
            sprintf('Unable to access %s', $this->getApiUrl())
          );
        }
        throw $x;
      }
    }
    catch (\Exception $x) {
      if ($throwException) {
        throw $x;
      }
    }

    return FALSE;
  }

  /**
   * Validate the given configuration array.
   *
   * @param array $config
   *   The configuration to validate.
   *
   * @return bool
   *   Returns TRUE if the config was valid. Throws an exception otherwise.
   */
  protected function validateConfig(array $config) {

    $required = ['site_group', 'environment', 'username', 'api_key'];
    foreach ($required as $key) {
      if (empty($config[$key])) {
        throw new InvalidConfigurationException(
          sprintf('Missing %s configuration.', $key)
        );
      }
    }

    return TRUE;
  }

  /**
   * Make an API request.
   *
   * @param string $http_method
   *   The HTTP method to use (GET, PUT, POST, DELETE).
   * @param string|array $api_method
   *   The API method to call.
   * @param array $options
   *   An array of options to pass with the API request.
   * @param int|null $api_version
   *   The version of the API to use. Defaults to 1.
   *
   * @return \swichers\Acsf\Client\ResponseInterface
   *   The response from the API.
   */
  protected function apiRequest(string $http_method, $api_method, array $options = [], int $api_version = NULL): ResponseInterface {

    // Allow swapping version on the fly if necessary.
    $options['base_uri'] = $this->getApiUrl($api_version ?: 1);
    $options['auth_basic'] =
      $this->config['username'] . ':' . $this->config['api_key'];

    return new Response(
      $this->httpClient->request(
        $http_method,
        $this->getMethodUrl($api_method),
        $options
      )
    );
  }

  /**
   * Convert the given method information into a usable API path.
   *
   * @param string|array $method
   *   A single or array of string.
   *
   * @return string
   *   The API method path to use.
   */
  protected function getMethodUrl($method): string {

    if (is_array($method)) {
      $method = implode('/', $method);
    }

    return $method;
  }

}
