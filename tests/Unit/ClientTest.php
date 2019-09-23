<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Client;
use swichers\Acsf\Client\Discovery\ActionManager;
use swichers\Acsf\Client\Discovery\EntityManager;
use swichers\Acsf\Client\Endpoints\Action\ActionBase;
use swichers\Acsf\Client\Endpoints\Action\ActionInterface;
use swichers\Acsf\Client\Endpoints\Entity\EntityBase;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\MissingEndpointException;
use swichers\Acsf\Client\ResponseInterface;
use Symfony\Component\HttpClient\MockHttpClient;

/**
 * Class ClientTest
 *
 * @coversDefaultClass \swichers\Acsf\Client\Client
 */
class ClientTest extends TestCase {

  protected $mockHttp;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Discovery\ActionManagerInterface
   */
  protected $mockActionManager;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject|\swichers\Acsf\Client\Discovery\EntityManagerInterface
   */
  protected $mockEntityManager;

  /**
   * @covers ::__construct
   */
  public function testConstructClient() {

    $this->assertInstanceOf(Client::class, $this->getClient());
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
  }

  /**
   * @covers ::setConfig
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidConfigurationException
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoConfig() {

    $client = $this->getClient();
    $client->setConfig([]);
  }

  /**
   * @covers ::setConfig
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidConfigurationException
   *
   * @depends testSetConfig
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailEmptyConfig() {

    $client = $this->getClient();
    $client->setConfig([
      'username' => NULL,
      'api_key' => NULL,
      'domain' => NULL,
      'environment' => NULL,
    ]);
  }

  /**
   * @covers ::setConfig
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidConfigurationException
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoEnv() {

    $client = $this->getClient();
    $client->setConfig([
      'username' => 'abc',
      'domain' => 'abc',
      'api_key' => 'abc',
    ]);
  }

  /**
   * @covers ::setConfig
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidConfigurationException
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoApiKey() {

    $client = $this->getClient();
    $client->setConfig([
      'username' => 'abc',
      'domain' => 'abc',
      'environment' => 'abc',
    ]);
  }

  /**
   * @covers ::setConfig
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidConfigurationException
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoDomain() {

    $client = $this->getClient();
    $client->setConfig([
      'username' => 'abc',
      'api_key' => 'abc',
      'environment' => 'abc',
    ]);
  }

  /**
   * @covers ::setConfig
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidConfigurationException
   *
   * @depends testSetConfig
   */
  public function testSetConfigFailNoUsername() {

    $client = $this->getClient();
    $client->setConfig([
      'api_key' => 'abc',
      'domain' => 'abc',
      'environment' => 'abc',
    ]);
  }

  /**
   * @covers ::getApiUrl
   *
   * @depends testSetConfig
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
   * @covers ::testConnection
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidCredentialsException
   */
  public function testTestConnectionFailCredsExcept() {

    $client = $this->getClient(['username' => 'abc123']);
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

    $resp = $client->apiGet(['Unit', 'Test'], [], 2);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals('https://www.ligula-dapibus.acsitefactory.com/api/v2/Unit/Test', $resp->getOriginalResponse()
      ->getInfo('url'));
  }

  /**
   * @covers ::apiPost
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiPost() {

    $resp = $this->getClient()->apiPost('Unit/Test', []);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals('https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test', $resp->getOriginalResponse()
      ->getInfo('url'));
  }

  /**
   * @covers ::apiDelete
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiDelete() {

    $resp = $this->getClient()->apiDelete('Unit/Test', []);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals('https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test', $resp->getOriginalResponse()
      ->getInfo('url'));
  }

  /**
   * @covers ::apiPut
   * @covers ::apiRequest
   * @covers ::getMethodUrl
   */
  public function testApiPut() {

    $resp = $this->getClient()->apiPut('Unit/Test', []);
    $this->assertInstanceOf(ResponseInterface::class, $resp);
    $this->assertEquals('https://www.ligula-dapibus.acsitefactory.com/api/v1/Unit/Test', $resp->getOriginalResponse()
      ->getInfo('url'));
  }

  /**
   * @covers ::getAction
   */
  public function testGetAction() {

    $client = $this->getClient();
    $this->assertInstanceOf(ActionInterface::class, $client->getAction('Status'));
  }

  /**
   * @covers ::getAction
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\MissingActionException
   */
  public function testGetActionFailType() {

    $client = $this->getClient();
    $client->getAction('ThisActionDoesNotExist');
  }

  /**
   * @covers ::getEntity
   */
  public function testGetEntity() {

    $client = $this->getClient();
    $this->assertInstanceOf(EntityInterface::class, $client->getEntity('Backup', 123));
  }

  /**
   * @covers ::getEntity
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\MissingEntityException
   *
   * @depends testGetEntity
   */
  public function testGetEntityFailType() {

    $client = $this->getClient();
    $client->getEntity('ThisEntityDoesNotExist', 123);
  }

  /**
   * @covers ::getEntity
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\MissingEndpointException
   *
   * @depends testGetEntity
   */
  public function testGetEntityFailId() {

    $client = $this->getClient();
    $client->getEntity('Backup', 456);
  }

  protected function setUp() {

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
      ->willReturnCallback(function ($name, $client, $id, ...$other) use ($mockEntity) {

        if ('Backup' == $name && $id == 123) {
          return $mockEntity;
        }

        throw new MissingEndpointException();
      });
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
