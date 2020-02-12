<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Discovery\ActionManager;
use swichers\Acsf\Client\Discovery\EntityManager;
use swichers\Acsf\Client\Endpoints\Action\AbstractAction;
use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Endpoints\Entity\AbstractEntity;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\InvalidConfigurationException;
use swichers\Acsf\Client\Exceptions\InvalidCredentialsException;
use swichers\Acsf\Client\Exceptions\MissingActionException;
use swichers\Acsf\Client\Exceptions\MissingEndpointException;
use swichers\Acsf\Client\Exceptions\MissingEntityException;
use swichers\Acsf\Client\ResponseInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Tests for the main ACSF Client class.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Client
 *
 * @group AcsfClient
 */
class ClientTest extends TestCase {

  /**
   * A mocked Action Manager.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Discovery\ActionManagerInterface
   */
  protected $mockActionManager;

  /**
   * A mocked Entity Manager.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Discovery\EntityManagerInterface
   */
  protected $mockEntityManager;

  /**
   * Basic coverage of the constructor.
   *
   * @covers ::__construct
   */
  public function testConstructClient() {

    $this->assertInstanceOf(Client::class, $this->getClient());
  }

  /**
   * Validate we can set the configuration.
   *
   * @covers ::setConfig
   * @covers ::getConfig
   * @covers ::validateConfig
   */
  public function testSetConfig() {

    $client = $this->getClient();

    $defaultConfig = $client->getConfig();

    $newConfig = ['environment' => 'test'] + $defaultConfig;
    $this->assertEquals($defaultConfig, $client->setConfig($newConfig));
    $this->assertEquals($newConfig, $client->getConfig());
  }

  /**
   * Validate we can get the configuration.
   *
   * @covers ::setConfig
   * @covers ::validateConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoConfig() {

    $client = $this->getClient();

    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([]);
  }

  /**
   * Validate that setting will fail when required keys are empty.
   *
   * @covers ::setConfig
   * @covers ::validateConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailEmptyConfig() {

    $client = $this->getClient();
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig(
      [
        'username' => NULL,
        'api_key' => NULL,
        'site_group' => NULL,
        'environment' => NULL,
      ]
    );
  }

  /**
   * Validate setting will fail when environment is missing.
   *
   * @covers ::setConfig
   * @covers ::validateConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoEnv() {

    $client = $this->getClient();
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig(
      [
        'username' => 'abc',
        'site_group' => 'abc',
        'api_key' => 'abc',
      ]
    );
  }

  /**
   * Validate setting will fail when api_key is missing.
   *
   * @covers ::setConfig
   * @covers ::validateConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoApiKey() {

    $client = $this->getClient();
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig(
      [
        'username' => 'abc',
        'site_group' => 'abc',
        'environment' => 'abc',
      ]
    );
  }

  /**
   * Validate setting will fail when domain is missing.
   *
   * @covers ::setConfig
   * @covers ::validateConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoDomain() {

    $client = $this->getClient();
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig(
      [
        'username' => 'abc',
        'api_key' => 'abc',
        'environment' => 'abc',
      ]
    );
  }

  /**
   * Validate setting will fail when username is missing.
   *
   * @covers ::setConfig
   * @covers ::validateConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoUsername() {

    $client = $this->getClient();
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig(
      [
        'api_key' => 'abc',
        'site_group' => 'abc',
        'environment' => 'abc',
      ]
    );
  }

  /**
   * Validate we can get a correctly formatted API URL.
   *
   * @covers ::getApiUrl
   *
   * @depends testSetConfig
   */
  public function testGetApiUrl() {

    $client = $this->getClient(
      [
        'environment' => 'dev',
        'site_group' => 'example',
      ]
    );

    $this->assertEquals(
      'https://www.dev-example.acsitefactory.com/api/v1/',
      $client->getApiUrl()
    );
    $this->assertEquals(
      'https://www.dev-example.acsitefactory.com/api/v1/',
      $client->getApiUrl(-1)
    );
    $this->assertEquals(
      'https://www.dev-example.acsitefactory.com/api/v2/',
      $client->getApiUrl(2)
    );

    $config = ['environment' => 'abc123'] + $client->getConfig();
    $client->setConfig($config);
    $this->assertEquals(
      'https://www.abc123-example.acsitefactory.com/api/v1/',
      $client->getApiUrl()
    );
    $this->assertEquals(
      'https://www.abc123-example.acsitefactory.com/api/v1/',
      $client->getApiUrl(-1)
    );
    $this->assertEquals(
      'https://www.abc123-example.acsitefactory.com/api/v2/',
      $client->getApiUrl(2)
    );

    $config = ['environment' => 'live'] + $client->getConfig();
    $client->setConfig($config);
    $this->assertEquals(
      'https://www.example.acsitefactory.com/api/v1/',
      $client->getApiUrl()
    );
    $this->assertEquals(
      'https://www.example.acsitefactory.com/api/v1/',
      $client->getApiUrl(-1)
    );
    $this->assertEquals(
      'https://www.example.acsitefactory.com/api/v2/',
      $client->getApiUrl(2)
    );

    $config = ['environment' => 'new-config'] + $client->getConfig();
    $client->setConfig($config);
    $this->assertEquals(
      'https://www.new-config-example.acsitefactory.com/api/v1/',
      $client->getApiUrl()
    );
  }

  /**
   * Validate we can test for a valid connection.
   *
   * @covers ::testConnection
   *
   * @depends testSetConfig
   */
  public function testTestConnection() {

    $client = $this->getClient();
    $this->assertTrue($client->testConnection(FALSE));

    $client->setConfig(['username' => 'abc123'] + $client->getConfig());
    $this->assertFalse($client->testConnection(FALSE));
  }

  /**
   * Validate that we can get an InvalidCredentialsException.
   *
   * @covers ::testConnection
   *
   * @depends testTestConnection
   */
  public function testTestConnectionFailCredsExcept() {

    $client = $this->getClient(['username' => 'abc123']);
    $client->testConnection(FALSE);
    $client->testConnection(FALSE);
    $client->testConnection(FALSE);

    $this->expectException(InvalidCredentialsException::class);
    $client->testConnection(TRUE);
  }

  /**
   * Validate that we can get a failed request exception.
   *
   * @covers ::testConnection
   *
   * @depends testTestConnection
   */
  public function testTestConnectionFailClientExcept() {

    $client = $this->getClient(['username' => 'abc123']);
    $client->testConnection(FALSE);
    $client->testConnection(FALSE);

    $this->expectException(ClientException::class);
    $client->testConnection(TRUE);
  }

  /**
   * Validate that we can get a general exception.
   *
   * @covers ::testConnection
   *
   * @depends testTestConnection
   */
  public function testTestConnectionFailGeneral() {

    $client = $this->getClient(['username' => 'abc123']);
    $client->testConnection(FALSE);

    $this->expectException(Exception::class);
    $client->testConnection(TRUE);
  }

  /**
   * Validate our GET request builds correctly.
   *
   * @covers ::apiGet
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiGet() {

    $client = $this->getClient();
    $resp = $client->apiGet('Unit/Test');
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );

    $resp = $client->apiGet(['Unit', 'Test']);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );

    $resp = $client->apiGet(['Unit', 'Test'], [], 2);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v2/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );
  }

  /**
   * Validate our POST request builds correctly.
   *
   * @covers ::apiPost
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiPost() {

    $resp = $this->getClient()->apiPost('Unit/Test', []);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );

    $resp = $this->getClient()->apiPost('Unit/Test', [], 2);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v2/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );
  }

  /**
   * Validate our DELETE request builds correctly.
   *
   * @covers ::apiDelete
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiDelete() {

    $resp = $this->getClient()->apiDelete('Unit/Test', []);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );

    $resp = $this->getClient()->apiDelete('Unit/Test', [], 2);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v2/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );
  }

  /**
   * Validate our PUT request builds correctly.
   *
   * @covers ::apiPut
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiPut() {

    $resp = $this->getClient()->apiPut('Unit/Test', []);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );

    $resp = $this->getClient()->apiPut('Unit/Test', [], 2);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals(
      'https://www.ligula-dapibus.acsitefactory.com/api/v2/Unit/Test',
      $resp->getOriginalResponse()->getInfo('url')
    );
  }

  /**
   * Validate we can get an Action.
   *
   * @covers ::getAction
   */
  public function testGetAction() {

    $client = $this->getClient();
    $this->assertInstanceOf(
      ActionInterface::class,
      $client->getAction('Status')
    );
  }

  /**
   * Validate invalid Actions cause an Exception.
   *
   * @covers ::getAction
   */
  public function testGetActionFailType() {

    $client = $this->getClient();
    $this->expectException(MissingActionException::class);
    $client->getAction('ThisActionDoesNotExist');
  }

  /**
   * Validate we can get an Entity.
   *
   * @covers ::getEntity
   */
  public function testGetEntity() {

    $client = $this->getClient();
    $this->assertInstanceOf(
      EntityInterface::class,
      $client->getEntity('Backup', 123)
    );
  }

  /**
   * Validate an invalid Entity type causes an exception.
   *
   * @covers ::getEntity
   *
   * @depends testGetEntity
   */
  public function testGetEntityFailType() {

    $client = $this->getClient();
    $this->expectException(MissingEntityException::class);
    $client->getEntity('ThisEntityDoesNotExist', 123);
  }

  /**
   * Validate an invalid Entity ID causes an exception.
   *
   * @covers ::getEntity
   *
   * @depends testGetEntity
   */
  public function testGetEntityFailId() {

    $client = $this->getClient();
    $this->expectException(MissingEndpointException::class);
    $client->getEntity('Backup', 456);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {

    parent::setUp();

    $status_check = function () {

      static $calls = 0;
      // Call 1 is in the constructor.
      // Call N+1 is the actual method call.
      $calls++;

      // Simulate a non-ClientException.
      if (3 == $calls) {
        throw new Exception();
      }
      // Simulate a 404.
      elseif (4 == $calls) {
        $coded_response = new class() extends MockResponse {

          /**
           * {@inheritdoc}
           */
          public function getStatusCode(): int {

            return 404;
          }

        };
        MockResponse::fromRequest(
          'GET',
          'http://example.com',
          [],
          $coded_response
        );
        throw new ClientException($coded_response);
      }
      // Simulate a 403.
      elseif (5 == $calls) {
        $coded_response = new class() extends MockResponse {

          /**
           * {@inheritdoc}
           */
          public function getStatusCode(): int {

            return 403;
          }

        };
        MockResponse::fromRequest(
          'GET',
          'http://example.com',
          [],
          $coded_response
        );

        throw new ClientException($coded_response);
      }

      return TRUE;
    };

    /** @var \swichers\Acsf\Client\Endpoints\Action\ActionInterface|\PHPUnit\Framework\MockObject\MockObject $mockAction */
    $mockAction = $this->getMockBuilder(AbstractAction::class)->setMethods(
      ['ping']
    )->disableOriginalConstructor()->getMockForAbstractClass();
    $mockAction->method('ping')->willReturnCallback($status_check);

    /** @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Discovery\ActionManagerInterface $mockActionManager */
    $mockActionManager =
      $this->getMockBuilder(ActionManager::class)
        ->setMethods(['get', 'create'])
        ->disableOriginalConstructor()
        ->getMock();
    $mockActionManager->method('get')->willReturnMap(
      [
        [
          'Status',
          ['foo' => 'bar'],
        ],
      ]
    );
    $mockActionManager->method('create')->willReturnCallback(
      function ($name, ...$other) use ($mockAction) {

        if ('Status' === $name) {
          return $mockAction;
        }

        return NULL;
      }
    );

    /** @var \swichers\Acsf\Client\Endpoints\Entity\EntityInterface|\PHPUnit\Framework\MockObject\MockObject $mockEntity */
    $mockEntity =
      $this->getMockBuilder(AbstractEntity::class)
        ->setMethods([])
        ->disableOriginalConstructor()
        ->getMockForAbstractClass();

    /** @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Discovery\EntityManagerInterface $mockEntityManager */
    $mockEntityManager =
      $this->getMockBuilder(EntityManager::class)
        ->setMethods(['get', 'create'])
        ->disableOriginalConstructor()
        ->getMock();
    $mockEntityManager->method('get')->willReturnMap(
      [
        [
          'Backup',
          ['foo' => 'bar'],
        ],
      ]
    );
    $mockEntityManager->method('create')->willReturnCallback(
      function ($name, $client, $id, ...$other) use ($mockEntity) {

        if ('Backup' === $name && 123 === $id) {
          return $mockEntity;
        }

        throw new MissingEndpointException();
      }
    );

    $this->mockActionManager = $mockActionManager;
    $this->mockEntityManager = $mockEntityManager;
  }

  /**
   * Get a client object with mocked dependencies.
   *
   * @param array $config
   *   An array of config information to use.
   *
   * @return \swichers\Acsf\Client\Client
   *   An ACSF Client.
   */
  protected function getClient(array $config = []) {

    $defaultConfig = [
      'username' => 'Nulla Etiam nisi',
      'api_key' => 'Donec justo, venenatis tellus',
      'site_group' => 'dapibus',
      'environment' => 'ligula',
    ];

    return new Client(
      new MockHttpClient(),
      $this->mockActionManager,
      $this->mockEntityManager,
      $config + $defaultConfig
    );
  }

}
