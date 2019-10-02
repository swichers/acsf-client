<?php declare(strict_types = 1);

namespace swichers\Acsf\Client;

use Symfony\Contracts\HttpClient\ResponseInterface as HttpClientResponseInterface;

interface ResponseInterface {

  /**
   * Return the original HttpClient Response.
   *
   * @return \Symfony\Contracts\HttpClient\ResponseInterface
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
   *
   * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
   * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
   * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
   * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
   * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
   */
  public function toArray(bool $throw = TRUE): array;

}
