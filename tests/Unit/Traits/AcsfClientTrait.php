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

      $data = ['internal_method' => $api_method] + $options;

      // We specifically handle getting the environments because this endpoint
      // implementation looks at the returned data.
      if ('stage' === $api_method && 'GET' === $http_method) {
        $data = [
          'environments' => [
            'dev' => 'dev',
            'test' => 'test',
            'live' => 'live',
          ],
        ];
      }
      elseif ('wip/task/1234/status' === $api_method &&
        'GET' === $http_method) {

        static $completed_time = 0;
        static $error_time = 0;
        static $calls = 0;

        $calls++;

        if (3 === $calls && empty($completed_time)) {
          $completed_time = time();
          $calls = 0;
        }
        elseif (5 === $calls && empty($error_time)) {
          $error_time = time();
          $calls = 0;
        }
        elseif (6 <= $calls) {
          $completed_time = 0;
          $error_time = 0;
          $calls = 0;
        }

        $data['wip_task'] = [
          'completed' => $completed_time,
          'error' => $error_time,
        ];
      }

      $mockResp = new MockResponse(json_encode($data));
      $mockHttp = new MockHttpClient($mockResp);

      return new Response($mockHttp->request('GET', 'http://example.com'));
    };

    $mockClient->method('apiRequest')->willReturnCallback($callback);

    return $mockClient;
  }

}
