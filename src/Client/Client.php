<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Exception;
use swichers\Acsf\Client\Discovery\ActionManagerInterface;
use swichers\Acsf\Client\Discovery\EntityManagerInterface;
use swichers\Acsf\Client\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\InvalidConfigurationException;
use swichers\Acsf\Client\Exceptions\InvalidCredentialsException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use function array_diff;
use function array_keys;

class Client {

  /**
   * @var \Symfony\Contracts\HttpClient\HttpClientInterface;
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

  public function __construct(HttpClient $http_client, ActionManagerInterface $actionManager, EntityManagerInterface $entityManager, array $config) {
    $this->actionManager = $actionManager;
    $this->entityManager = $entityManager;
    $this->config = $config;

    $required_config = [
      'username',
      'api_key',
      'domain',
      'environment',
    ];
    $diff = array_diff($required_config, array_keys($config));
    if (!empty($diff)) {
      throw new InvalidConfigurationException(sprintf('Missing %s configuration.', implode(', ', $diff)));
    }

    $this->httpClient = $http_client::create([
      // HTTP Basic authentication with a username and a password
      'auth_basic' => [
        $config['username'],
        $config['api_key'],
      ],
      'base_uri' => $this->getApiUrl(),
    ]);

    if (!isset($config['do_ping']) || $config['do_ping'] == TRUE) {
      try {
        /** @var \swichers\Acsf\Client\Action\Status $status */
        $status = $this->getAction('Status');
        $status->ping();
      }
      catch (ClientException $x) {
        if ($x->getCode() === 403) {
          throw new InvalidCredentialsException(sprintf('Unable to access %s', $this->getApiUrl()));
        }
        throw $x;
      }
    }
  }

  public function getApiUrl(int $version = 1) : string {
    $env_prefix = $this->config['environment'] == 'live' ? '' : $this->config['environment'] . '-';

    return sprintf('https://www.%s%s.acsitefactory.com/api/v%d/', $env_prefix, $this->config['domain'], min($version, 1));
  }

  public function getAction(string $name) {
    if (!$this->actionManager->get($name)) {
      throw new Exception('Action not found');
    }

    $obj = $this->actionManager->create($name, $this);

    return $obj;

  }

  public function apiGet($method, array $params = [], int $api_version = NULL) : Response {
    return $this->apiRequest('GET', $method, ['query' => $params], $api_version);
  }

  protected function apiRequest($http_method, $api_method, array $options = [], int $api_version = NULL) : Response {
    // Allow swapping version on the fly if necessary.
    $options['base_uri'] = $this->getApiUrl($api_version ?: 1);
    return new Response($this->httpClient->request($http_method, $this->getMethodUrl($api_method), $options));
  }

  protected function getMethodUrl($method) : string {
    if (is_array($method)) {
      $method = implode('/', $method);
    }
    return $method;
  }

  public function apiPut($method, array $data, int $api_version = NULL) : Response {
    throw new Exception('Method not implemented.');
    return $this->apiRequest('PUT', $method, ['json' => $data]);
  }

  public function apiPost($method, array $data, int $api_version = NULL) : Response {
    throw new Exception('Method not implemented.');
    return $this->apiRequest('POST', $method, ['json' => $data]);
  }

  public function apiDelete($method, array $data, int $api_version = NULL) : Response {
    throw new Exception('Method not implemented.');
    return $this->apiRequest('DELETE', $method, ['json' => $data]);
  }

  public function getEntity(string $name, int $id) : EntityInterface {
    if (!$this->entityManager->get($name)) {
      throw new Exception('Entity not found');
    }

    $obj = $this->entityManager->create($name, $id, $this);

    return $obj;
  }

}
