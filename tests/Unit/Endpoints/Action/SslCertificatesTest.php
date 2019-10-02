<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Action\SslCertificates;

/**
 * Tests for the SslCertificatesTest Action.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\Action\SslCertificates
 *
 * @group AcsfClient
 */
class SslCertificatesTest extends AbstractActionTestBase {

  /**
   * Validate we can get a list of SSL certs.
   *
   * @covers ::list
   */
  public function testList() {

    $action = new SslCertificates($this->getMockAcsfClient());

    $result = $action->list(['random_test' => TRUE, 'limit' => -1]);
    $this->assertEquals('ssl/certificates', $result['internal_method']);
    $this->assertEquals(['stack_id' => 1, 'limit' => 1], $result['query']);

    $result = $action->list(['stack_id' => -1]);
    $this->assertEquals(1, $result['query']['stack_id']);
    $result = $action->list(['stack_id' => 20]);
    $this->assertEquals(20, $result['query']['stack_id']);
  }

  /**
   * Validate we can install new SSL certs.
   *
   * @covers ::create
   */
  public function testCreate() {

    $action = new SslCertificates($this->getMockAcsfClient());
    $options = [
      'stack_id' => -1,
      'ca_certificates' => 'UnitTestCA',
      'another key' => TRUE,
    ];
    $result = $action->create(
      'UnitTest',
      'UnitTestCert',
      'UnitTestPrivKey',
      $options
    );
    $this->assertEquals('ssl/certificates', $result['internal_method']);
    $this->assertEquals('UnitTest', $result['json']['label']);
    $this->assertEquals('UnitTestCert', $result['json']['certificate']);
    $this->assertEquals('UnitTestPrivKey', $result['json']['private_key']);
    $this->assertEquals('UnitTestCA', $result['json']['ca_certificates']);
    $this->assertEquals(1, $result['json']['stack_id']);
    $this->assertArrayNotHasKey('another key', $result['json']);

    $result = $action->create(
      'UnitTest',
      'UnitTestCert',
      'UnitTestPrivKey',
      ['stack_id' => 3]
    );
    $this->assertEquals(3, $result['json']['stack_id']);
  }

}
