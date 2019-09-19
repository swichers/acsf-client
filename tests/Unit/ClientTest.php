<?php declare(strict_types=1);

namespace swichers\Acsf\Tests;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Discovery\ActionManager;
use swichers\Acsf\Client\Discovery\EntityManager;
use swichers\Acsf\Client\Endpoints\Action\ActionBase;
use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Endpoints\Entity\EntityBase;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\InvalidConfigurationException;
use swichers\Acsf\Client\Exceptions\InvalidCredentialsException;
use swichers\Acsf\Client\Exceptions\MissingActionException;
use swichers\Acsf\Client\Exceptions\MissingEntityException;
use swichers\Acsf\Client\ResponseInterface;
use Symfony\Component\HttpClient\MockHttpClient;

/**
 * Class ClientTest
 *
 * @package swichers\Acsf\Tests
 * @coversDefaultClass \swichers\Acsf\Client\Client
 */
class ClientTest extends TestCase {

  protected $mockHttp;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject |
   *   \swichers\Acsf\Client\Discovery\ActionManager
   */
  protected $mockActionManager;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject |
   *   \swichers\Acsf\Client\Discovery\EntityManager
   */
  protected $mockEntityManager;

  /**
   * @covers ::__construct
   */
  public function testConstructClient() {

    $this->assertInstanceOf(Client::class, $this->getClient());
  }

  public function setUp() {

    parent::setUp();

    $this->mockHttp = new MockHttpClient();

    $mockAction = $this->getMockBuilder(ActionBase::class)
      ->setMethods(['ping'])
      ->disableOriginalConstructor()
      ->getMockForAbstractClass();

    $this->mockActionManager = $this->getMockBuilder(ActionManager::class)
      ->setMethods(['get', 'create'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->mockActionManager->method('get')->willReturnMap([
      [
        'Status',
        ['foo' => 'bar'],
      ],
    ]);
    $this->mockActionManager->method('create')
      ->willReturnCallback(function ($name, ...$other) use ($mockAction) {

        if ('Status' == $name) {
          return $mockAction;
        }

        return NULL;
      });

    $mockEntity = $this->getMockBuilder(EntityBase::class)
      ->setMethods([])
      ->disableOriginalConstructor()
      ->getMockForAbstractClass();

    $this->mockEntityManager = $this->getMockBuilder(EntityManager::class)
      ->setMethods(['get', 'create'])
      ->disableOriginalConstructor()
      ->getMock();
    $this->mockEntityManager->method('get')->willReturnMap([
      [
        'Backup',
        ['foo' => 'bar'],
      ],
    ]);
    $this->mockEntityManager->method('create')
      ->willReturnCallback(function ($name, $id, ...$other) use ($mockEntity) {

        if ('Backup' == $name && $id == 123) {
          return $mockEntity;
        }

        return NULL;
      });
  }

  /**
   * @covers ::setConfig
   * @covers ::getConfig
   * @covers ::validateConfig
   */
  public function testSetConfig() {

    $client = $this->getClient();

    $default_config = $client->getConfig();

    $new_config = ['environment' => 'test'] + $default_config;
    $this->assertEquals($default_config, $client->setConfig($new_config));
    $this->assertEquals($new_config, $client->getConfig());

    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([]);

    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([
      'username' => NULL,
      'api_key' => NULL,
      'domain' => NULL,
      'environment' => NULL,
    ]);

    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([
      'username' => NULL,
      'api_key' => NULL,
      'domain' => NULL,
    ]);
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([
      'username' => NULL,
      'api_key' => NULL,
      'environment' => NULL,
    ]);
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([
      'username' => NULL,
      'domain' => NULL,
      'environment' => NULL,
    ]);
    $this->expectException(InvalidConfigurationException::class);
    $client->setConfig([
      'api_key' => NULL,
      'domain' => NULL,
      'environment' => NULL,
    ]);
  }

  /**
   * @covers ::getApiUrl
   */
  public function testGetApiUrl() {

    $client = $this->getClient(['environment' => 'dev', 'domain' => 'example']);

    $this->assertEquals('https://www.dev-example.acsitefactory.com/api/v1/', $client->getApiUrl());
    $this->assertEquals('https://www.dev-example.acsitefactory.com/api/v1/', $client->getApiUrl(-1));
    $this->assertEquals('https://www.dev-example.acsitefactory.com/api/v2/', $client->getApiUrl(2));

    $config = ['environment' => 'abc123'] + $client->getConfig();
    $client->setConfig($config);
    $this->assertEquals('https://www.abc123-example.acsitefactory.com/api/v1/', $client->getApiUrl());
    $this->assertEquals('https://www.abc123-example.acsitefactory.com/api/v1/', $client->getApiUrl(-1));
    $this->assertEquals('https://www.abc123-example.acsitefactory.com/api/v2/', $client->getApiUrl(2));

    $config = ['environment' => 'live'] + $client->getConfig();
    $client->setConfig($config);
    $this->assertEquals('https://www.example.acsitefactory.com/api/v1/', $client->getApiUrl());
    $this->assertEquals('https://www.example.acsitefactory.com/api/v1/', $client->getApiUrl(-1));
    $this->assertEquals('https://www.example.acsitefactory.com/api/v2/', $client->getApiUrl(2));

    $config = ['environment' => 'new-config'] + $client->getConfig();
    $client->setConfig($config);
    $this->assertEquals('https://www.new-config-example.acsitefactory.com/api/v1/', $client->getApiUrl());
  }

  /**
   * @covers ::testConnection
   */
  public function testTestConnection() {

    $config = [
      'username' => 'abc',
      'api_key' => '123',
      'domain' => 'unit-test',
      'environment' => 'dev',
    ];
    $client = new Client($this->mockHttp, $this->mockActionManager, $this->mockEntityManager, $config);

    $this->assertTrue($client->testConnection(FALSE));
    $this->assertFalse($client->testConnection(FALSE));

    $this->expectException(InvalidCredentialsException::class);
    $this->assertTrue($client->testConnection(TRUE));

    $this->expectException(InvalidCredentialsException::class);
    $client->testConnection(TRUE);
  }

  /**
   * @covers ::apiGet
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiGet() {

    $client = $this->getClient();
    $resp = $client->apiGet('Unit/Test');
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals('https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test', $resp->getOriginalResponse()
      ->getInfo('url'));

    $resp = $client->apiGet(['Unit', 'Test']);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals('https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test', $resp->getOriginalResponse()
      ->getInfo('url'));
  }

  /**
   * @covers ::apiPost
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiPost() {

    $this->assertInstanceOf(ResponseInterface::class, $this->getClient()
      ->apiPost('Unit/Test', []));
  }

  /**
   * @covers ::apiDelete
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiDelete() {

    $this->assertInstanceOf(ResponseInterface::class, $this->getClient()
      ->apiDelete('Unit/Test', []));
  }

  /**
   * @covers ::apiPut
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiPut() {

    $this->assertInstanceOf(ResponseInterface::class, $this->getClient()
      ->apiPut('Unit/Test', []));
  }

  /**
   * @covers ::getAction
   */
  public function testGetAction() {

    $client = $this->getClient();

    $this->assertInstanceOf(ActionInterface::class, $client->getAction('Status'));
    $this->expectException(MissingActionException::class);
    $client->getAction('ThisActionDoesNotExist');
  }

  /**
   * @covers ::getEntity
   */
  public function testGetEntity() {

    $client = $this->getClient();

    $this->assertInstanceOf(EntityInterface::class, $client->getEntity('Backup', 123));
    $this->expectException(MissingEntityException::class);
    $client->getEntity('ThisEntityDoesNotExist', 123);

    $this->expectException(MissingEntityException::class);
    $this->assertInstanceOf(EntityInterface::class, $client->getEntity('Backup', 456));
  }

  protected function getClient(array $config = []) {

    $default_config = [
      'username' => 'Nulla Etiam nisi',
      'api_key' => 'Donec justo, venenatis tellus',
      'domain' => 'dapibus',
      'environment' => 'ligula',
    ];

    return new Client($this->mockHttp, $this->mockActionManager, $this->mockEntityManager, $config +
      $default_config);
  }

}
