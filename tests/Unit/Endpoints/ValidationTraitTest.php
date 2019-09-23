<?php

namespace swichers\Acsf\Client\Tests\Endpoints;

use PHPUnit\Framework\TestCase;
use swichers\Acsf\Client\Endpoints\ValidationTrait;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Class ValidationTraitTest
 *
 * @package swichers\Acsf\Client\Tests\Endpoints
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\ValidationTrait
 */
class ValidationTraitTest extends TestCase {

  /**
   * @covers ::limitOptions
   */
  public function testLimitOptions() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'limitOptions');

    $options = [
      'limit',
      'disallowed',
    ];
    $result = $method->invoke($object, array_flip($options), ['limit']);
    $this->assertEquals(['limit' => 0], $result);
  }

  /**
   * @covers ::constrictPaging
   */
  public function testConstrictPaging() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'constrictPaging');

    $options = [
      'limit' => -1,
      'page' => -1,
      'order' => 'DESC',
      'random' => 'random',
    ];

    $result = $method->invoke($object, $options);
    $this->assertEquals(1, $result['limit']);
    $this->assertEquals(1, $result['page']);
    $this->assertEquals('desc', $result['order']);
    $this->assertEquals('random', $result['random']);
    $this->assertEquals('asc', $method->invoke($object, ['order' => 'AsC'])['order']);
    $this->assertEquals('desc', $method->invoke($object, ['order' => 'ANYTHING'])['order']);
    $this->assertEquals(100, $method->invoke($object, ['limit' => 2000])['limit']);
    $this->assertEquals(10, $method->invoke($object, ['limit' => 2000], 10)['limit']);
  }

  /**
   * @covers ::ensureSortOrder
   */
  public function testEnsureSortOrder() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'ensureSortOrder');

    $this->assertEquals('asc', $method->invoke($object, 'asc'));
    $this->assertEquals('asc', $method->invoke($object, 'ASC'));
    $this->assertEquals('desc', $method->invoke($object, 'desc'));
    $this->assertEquals('desc', $method->invoke($object, 'DESC'));
    $this->assertEquals('desc', $method->invoke($object, 'abc123'));
  }

  /**
   * @covers ::cleanIntArray
   */
  public function testCleanIntArray() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'cleanIntArray');

    $data = [
      'string',
      '  1234',
      5678,
      5678,
      5678,
      new \stdClass(),
      '',
      NULL,
    ];

    $this->assertEquals([1234, 5678], $method->invoke($object, $data));
  }

  /**
   * @covers ::ensureBool
   */
  public function testEnsureBool() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'ensureBool');
    $this->assertTrue($method->invoke($object, 'true'));
    $this->assertTrue($method->invoke($object, 'yes'));
    $this->assertTrue($method->invoke($object, 'on'));
    $this->assertTrue($method->invoke($object, '1'));
    $this->assertTrue($method->invoke($object, 1));
    $this->assertTrue($method->invoke($object, TRUE));

    $this->assertFalse($method->invoke($object, 'false'));
    $this->assertFalse($method->invoke($object, 'no'));
    $this->assertFalse($method->invoke($object, 'off'));
    $this->assertFalse($method->invoke($object, '0'));
    $this->assertFalse($method->invoke($object, 'anything else'));
    $this->assertFalse($method->invoke($object, -1));
    $this->assertFalse($method->invoke($object, 0));
    $this->assertFalse($method->invoke($object, FALSE));

  }

  /**
   * @covers ::validateBackupOptions
   */
  public function testValidateBackupOptions() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');

    $options = [
      'callback_url' => 'http://example.com',
      'callback_method' => 'POST',
      'caller_data' => [],
      'components' => ['database', 'public files'],
    ];

    $expected = $options;
    $expected['caller_data'] = json_encode($expected['caller_data']);
    $this->assertEquals($expected, $method->invoke($object, $options));
    $this->assertEquals(['caller_data' => 'test'], $method->invoke($object, ['caller_data' => 'test']));


  }

  /**
   * @covers ::validateBackupOptions
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testValidateBackupOptionsBadUrl() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');
    $method->invoke($object, $options = ['callback_url' => '']);
  }

  /**
   * @covers ::validateBackupOptions
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testValidateBackupOptionsBadMethod() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');
    $method->invoke($object, $options = ['callback_method' => '']);
  }

  /**
   * @covers ::validateBackupOptions
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testValidateBackupOptionsNotArrayComponents() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');
    $method->invoke($object, $options = ['components' => '']);
  }

  /**
   * @covers ::validateBackupOptions
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testValidateBackupOptionsNotAllowedComponents() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');
    $method->invoke($object, $options = ['components' => ['not allowed']]);
  }

  /**
   * @covers ::requireOneOf
   */
  public function testRequireOneOf() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requireOneOf');

    $this->assertTrue($method->invoke($object, 'match', ['MaTcH']));
  }

  /**
   * @covers ::requireOneOf
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRequireOneOfFailCase() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requireOneOf');
    $method->invoke($object, 'no match', ['No MaTcH'], FALSE);
  }

  /**
   * @covers ::requirePatternMatch
   */
  public function testRequirePatternMatch() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requirePatternMatch');
    $this->assertTrue($method->invoke($object, 'match me', '/^match me$/'));
  }

  /**
   * @covers ::requirePatternMatch
   *
   * @expectedException \swichers\Acsf\Client\Exceptions\InvalidOptionException
   */
  public function testRequirePatternMatchFailRegex() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requirePatternMatch');
    $method->invoke($object, '   match me', '/^match me$/');
  }

  /**
   * @covers ::filterArrayToValues
   */
  public function testFilterArrayToValues() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'filterArrayToValues');

    $data = [
      'allowed',
      'not allowed',
      'ALLOWED',
      'ALSO allowed',
    ];

    $this->assertEquals(['allowed'], $method->invoke($object, $data, ['allowed']));
    $this->assertEquals(['allowed'], $method->invoke($object, $data, ['allowed'], TRUE));
    $this->assertEquals([
      'allowed',
      'also allowed',
    ], $method->invoke($object, $data, ['allowed', 'also allowed'], TRUE));
  }

  protected function getWrappedTrait() {

    $wrapper = new class() {

      use ValidationTrait;
    };

    return $wrapper;
  }

  protected function getInvokableMethod($object, $methodName) {

    $ref = new \ReflectionObject($object);
    $method = $ref->getMethod($methodName);
    $method->setAccessible(TRUE);

    return $method;
  }

}
