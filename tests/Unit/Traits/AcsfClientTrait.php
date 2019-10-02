<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Traits;

use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Response;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Trait AcsfClientTrait.
 *
 * Reusable code for getting a mocked ACSF Client.
 *
 * @group AcsfClient
 */
trait AcsfClientTrait {

  /**
   * A mock ACSF Client.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\ClientInterface
   */
  protected $mockClient;

  /**
   * Get a mocked ACSF Client.
   *
   * @return \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\ClientInterface
   *   A mocked ACSF Client.
   */
  protected function getMockAcsfClient() {

    /** @var \PHPUnit\Framework\MockObject\MockObject $mockClient */
    $mockClient = $this->getMockBuilder(Client::class)->setMethods(
      ['apiRequest']
    )->disableOriginalConstructor()->getMock();

    // Stand-in for an actual request. It just returns whatever it is given
    // along with the URL that was used to call it.
    $callback = function ($http_method, $api_method, array $options = []) {

      $api_method =
        is_array($api_method) ? implode('/', $api_method) : $api_method;

      $data = json_encode(['internal_method' => $api_method] + $options);
      // We specifically handle getting the environments because this endpoint
      // implementation looks at the returned data.
      if ('stage' === $api_method && 'GET' === $http_method) {
        $data = json_encode(
          [
            'environments' => [
              'dev' => 'dev',
              'test' => 'test',
              'live' => 'live',
            ],
          ]
        );
      }

      $mockResp = new MockResponse($data);
      $mockHttp = new MockHttpClient($mockResp);

      return new Response($mockHttp->request('GET', 'http://example.com'));
    };

    $mockClient->method('apiRequest')->willReturnCallback($callback);

    return $mockClient;
  }

}
