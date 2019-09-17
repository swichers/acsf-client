<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Symfony\Contracts\HttpClient\ResponseInterface as SymfonyResponseInterface;

class Response implements ResponseInterface {

  protected $originalResponse;

  public function __construct(SymfonyResponseInterface $response) {
    $this->originalResponse = $response;
  }

  public function getOriginalResponse() : SymfonyResponseInterface {
    return $this->originalResponse;
  }

  public function toArray(bool $throw = TRUE) {
    return $this->originalResponse->toArray($throw);
  }
}
