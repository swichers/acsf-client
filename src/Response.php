<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Symfony\Contracts\HttpClient\ResponseInterface as SymfonyResponseInterface;

/**
 * API endpoint response wrapper.
 */
class Response implements ResponseInterface {

  /**
   * The original Symfony response.
   *
   * @var \Symfony\Contracts\HttpClient\ResponseInterface
   */
  protected $originalResponse;

  /**
   * Response constructor.
   *
   * @param \Symfony\Contracts\HttpClient\ResponseInterface $response
   *   The Symfony Response to wrap.
   */
  public function __construct(SymfonyResponseInterface $response) {

    $this->originalResponse = $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalResponse(): SymfonyResponseInterface {

    return $this->originalResponse;
  }

  /**
   * {@inheritdoc}
   */
  public function toArray(bool $throw = TRUE): array {

    return $this->originalResponse->toArray($throw);
  }

}
