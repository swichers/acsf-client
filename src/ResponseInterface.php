<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Symfony\Contracts\HttpClient\ResponseInterface as HttpClientResponseInterface;

/**
 * Contract for responses.
 */
interface ResponseInterface {

  /**
   * Return the original HttpClient Response.
   *
   * @return \Symfony\Contracts\HttpClient\ResponseInterface
   *   The original HttpClient response.
   */
  public function getOriginalResponse(): HttpClientResponseInterface;

  /**
   * Get the response data as an array.
   *
   * @param bool $throw
   *   TRUE to throw an exception on error.
   *
   * @return array
   *   The response data formatted as an array.
   */
  public function toArray(bool $throw = TRUE): array;

}
