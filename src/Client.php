<?php declare(strict_types=1);

namespace swichers\Acsf\Client;

use Exception;
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

class Client implements ClientInterface {

  /**
   * @var \Symfony\Contracts\HttpClient\HttpClientInterface
   */
  protected $httpClient;

  /**
   * @var \swichers\Acsf\Client\Discovery\ActionManagerInterface
   */
  protected $actionManager;

  /**
   * @var \swichers\Acsf\Client\Discovery\EntityManagerInterface
   */
  protected $entityManager;

  protected $config;

  public function __construct(HttpClientInterface $httpClient, ActionManagerInterface $actionManager, EntityManagerInterface $entityManager, array $config) {

    $this->httpClient = $httpClient;
    $this->actionManager = $actionManager;
    $this->entityManager = $entityManager;
    $this->setConfig($config);
    $this->testConnection(TRUE);
  }

  public function apiDelete($method, array $data, int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('DELETE', $method, ['json' => $data]);
  }

  public function apiGet($method, array $params = [], int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('GET', $method, ['query' => $params], $api_version);
  }

  public function apiPost($method, array $data, int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('POST', $method, ['json' => $data]);
  }

  public function apiPut($method, array $data, int $api_version = NULL): ResponseInterface {

    return $this->apiRequest('PUT', $method, ['json' => $data]);
  }

  public function getAction(string $name): ActionInterface {

    if (!$this->actionManager->get($name)) {
      throw new MissingActionException(sprintf('Action %s was not registered with the client.', $name));
    }

    return $this->actionManager->create($name, $this);
  }

  public function getApiUrl(int $version = 1): string {

    $env_prefix = $this->config['environment'] == 'live' ? ''
      : $this->config['environment'] . '-';

    return sprintf('https://www.%s%s.acsitefactory.com/api/v%d/', $env_prefix, $this->config['domain'], max($version, 1));
  }

  public function getConfig(): array {

    return $this->config;
  }

  public function setConfig(array $config): array {

    $this->validateConfig($config);
    $old_config = $this->config ?? [];
    $this->config = $config;

    return $old_config;
  }

  public function getEntity(string $name, int $id): EntityInterface {

    if (!$this->entityManager->get($name)) {
      throw new MissingEntityException(sprintf('Entity %s was not registered with the client.', $name));
    }

    return $this->entityManager->create($name, $this, $id);
  }

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
          throw new InvalidCredentialsException(sprintf('Unable to access %s', $this->getApiUrl()));
        }
        throw $x;
      }
    }

    return FALSE;
  }

  protected function validateConfig(array $config) {

    $required = ['domain', 'environment', 'username', 'api_key'];
    foreach ($required as $key) {
      if (empty($config[$key])) {
        throw new InvalidConfigurationException(sprintf('Missing %s configuration.', $key));
      }
    }

    return TRUE;
  }

  protected function apiRequest($http_method, $api_method, array $options = [], int $api_version = NULL): ResponseInterface {

    if ($http_method !== 'GET') {
      throw new Exception(sprintf('Request method %s is not implemented.', $http_method));
    }
    // Allow swapping version on the fly if necessary.
    $options['base_uri'] = $this->getApiUrl($api_version ?: 1);
    $options['auth_basic'] = $this->config['username'] .
      ':' .
      $this->config['api_key'];

    return new Response($this->httpClient->request($http_method, $this->getMethodUrl($api_method), $options));
  }

  protected function getMethodUrl($method): string {

    if (is_array($method)) {
      $method = implode('/', $method);
    }

    return $method;
  }

}
