<?php declare(strict_types=1);

namespace swichers\Acsf\Client\Tests;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Response;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Class ResponseTest
 *
 * @package swichers\Acsf\Client
 * @coversDefaultClass \swichers\Acsf\Client\Response
 */
class ResponseTest extends TestCase {

  /**
   * @covers ::toArray
   */
  public function testToArray() {

    $client = new MockHttpClient(new MockResponse('{}'));
    $original = $client->request('GET', 'http://example.com');
    $resp = new Response($original);
    $this->assertEquals([], $resp->toArray(TRUE));
  }

  /**
   * @covers ::toArray
   *
   * @expectedException \Symfony\Component\HttpClient\Exception\TransportException
   */
  public function testToArrayFailTransport() {

    $client = new MockHttpClient();
    $original = $client->request('GET', 'http://example.com');
    $resp = new Response($original);
    $resp->toArray(TRUE);
  }

  /**
   * @covers ::getOriginalResponse
   * @covers ::__construct
   */
  public function testGetOriginalResponse() {

    $original = new MockResponse();
    $resp = new Response($original);

    $this->assertEquals($original, $resp->getOriginalResponse());
  }

}
