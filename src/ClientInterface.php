<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

interface ClientInterface {

  public function getApiUrl(int $version = 1): string;

  public function getAction(string $name): ActionInterface;

  public function apiGet(
    $method,
    array $params = [],
    int $api_version = NULL
  ): ResponseInterface;

  public function apiPut(
    $method,
    array $data,
    int $api_version = NULL
  ): ResponseInterface;

  public function apiPost(
    $method,
    array $data,
    int $api_version = NULL
  ): ResponseInterface;

  public function apiDelete(
    $method,
    array $data,
    int $api_version = NULL
  ): ResponseInterface;

  public function getEntity(string $name, int $id): EntityInterface;

  public function testConnection($throwException = FALSE): bool;

  public function setConfig(array $config): array;

  public function getConfig(): array;

}
