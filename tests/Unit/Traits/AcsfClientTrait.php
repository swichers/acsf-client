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
      ->setMethods(['apiRequest'])
      ->disableOriginalConstructor()
      ->getMock();

    $callback = function ($http_method, $api_method, array $options = []) {

      $api_method = is_array($api_method) ? implode('/', $api_method)
        : $api_method;

      $data = json_encode(['internal_method' => $api_method] + $options);
      if ($api_method == 'stage' && $http_method == 'GET') {
        $data = json_encode([
          'environments' => [
            'dev' => 'dev',
            'test' => 'test',
            'live' => 'live',
          ],
        ]);
      }

      $mockResp = new MockResponse($data);
      $mockHttp = new MockHttpClient($mockResp);

      return new Response($mockHttp->request('GET', 'http://example.com'));
    };

    $mockClient->method('apiRequest')->willReturnCallback($callback);

    return $mockClient;
  }

}
