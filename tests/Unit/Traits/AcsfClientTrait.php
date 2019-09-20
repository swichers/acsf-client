<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests\Traits;


use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Response;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

trait AcsfClientTrait {

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\ClientInterface
   */
  protected $mockClient;

  /**
   * @return \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\ClientInterface
   */
  protected function getMockAcsfClient() {

    /** @var \PHPUnit\Framework\MockObject\MockObject $mockClient */
    $mockClient = $this->getMockBuilder(Client::class)
      ->disableOriginalConstructor()
      ->getMock();

    $callback = function ($method, array $options = []) {

      $method = is_array($method) ? implode('/', $method) : $method;

      $data = json_encode(['internal_method' => $method] + $options);
      $mockResp = new MockResponse($data);
      $mockHttp = new MockHttpClient($mockResp);

      return new Response($mockHttp->request('GET', 'http://example.com'));
    };

    $mockClient->method('apiGet')->willReturnCallback($callback);
    $mockClient->method('apiDelete')->willReturnCallback($callback);
    $mockClient->method('apiPost')->willReturnCallback($callback);
    $mockClient->method('apiPut')->willReturnCallback($callback);

    return $mockClient;
  }

}
