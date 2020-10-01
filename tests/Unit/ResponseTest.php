<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Response;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Tests for our ACSF Client response wrapper.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Response
 *
 * @group AcsfClient
 */
class ResponseTest extends TestCase {

  /**
   * Validate our response can return an array.
   *
   * @covers ::toArray
   */
  public function testToArray() {

    $client = new MockHttpClient(new MockResponse('{}'));
    $original = $client->request('GET', 'http://example.com');
    $resp = new Response($original);
    $this->assertEquals([], $resp->toArray(TRUE));
  }

  /**
   * Validate we can throw an exception when asking for an array.
   *
   * @covers ::toArray
   */
  public function testToArrayFailTransport() {

    $client = new MockHttpClient();
    $original = $client->request('GET', 'http://example.com');
    $resp = new Response($original);

    $this->expectException(JsonException::class);
    $resp->toArray(TRUE);
  }

  /**
   * Validate we can get the original Symfony response.
   *
   * @covers ::getOriginalResponse
   * @covers ::__construct
   */
  public function testGetOriginalResponse() {

    $original = new MockResponse();
    $resp = new Response($original);

    $this->assertEquals($original, $resp->getOriginalResponse());
  }

}
