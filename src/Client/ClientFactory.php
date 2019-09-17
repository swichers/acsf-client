<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use swichers\Acsf\Client\Discovery\ActionManagerInterface;
use swichers\Acsf\Client\Discovery\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;

class ClientFactory {

  protected $httpClient;

  protected $actionManager;

  protected $entityManager;

  public function __construct(HttpClient $http_client, ActionManagerInterface $actionManager, EntityManagerInterface $entityManager) {
    $this->httpClient = $http_client;
    $this->actionManager = $actionManager;
    $this->entityManager = $entityManager;
  }

  public function getClient(array $config) {
    return new Client($this->httpClient, $this->actionManager, $this->entityManager, $config);
  }
}
