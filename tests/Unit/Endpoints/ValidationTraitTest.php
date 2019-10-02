<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Tests\Endpoints;

use PHPUnit\Framework\TestCase;
use ReflectionObject;
use stdClass;
use swichers\Acsf\Client\Endpoints\ValidationTrait;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Tests to verify our input validation routines do what we expect.
 *
 * @coversDefaultClass \swichers\Acsf\Client\Endpoints\ValidationTrait
 *
 * @group AcsfClient
 */
class ValidationTraitTest extends TestCase {

  /**
   * Validate our option limiting filters out keys.
   *
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
   * Validate we massage paging values to something sane.
   *
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
    $this->assertEquals(
      'asc',
      $method->invoke($object, ['order' => 'AsC'])['order']
    );
    $this->assertEquals(
      'desc',
      $method->invoke($object, ['order' => 'ANYTHING'])['order']
    );
    $this->assertEquals(
      100,
      $method->invoke($object, ['limit' => 2000])['limit']
    );
    $this->assertEquals(
      10,
      $method->invoke($object, ['limit' => 2000], 10)['limit']
    );
  }

  /**
   * Validate that our sort order checks handle ASC and DESC.
   *
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
   * Validate we can filter an array down to just IDs.
   *
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
      new stdClass(),
      '',
      NULL,
    ];

    $this->assertEquals([1234, 5678], $method->invoke($object, $data));
  }

  /**
   * Validate our human readable bool conversion works.
   *
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
   * Validate our backup checker does what we expect.
   *
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
    $this->assertEquals(
      ['caller_data' => 'test'],
      $method->invoke($object, ['caller_data' => 'test'])
    );
  }

  /**
   * Validate the backup checker throws an exception with a bad callback url.
   *
   * @covers ::validateBackupOptions
   *
   * @depends testValidateBackupOptions
   */
  public function testValidateBackupOptionsBadUrl() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');

    $this->expectException(InvalidOptionException::class);
    $method->invoke($object, ['callback_url' => '']);
  }

  /**
   * Validate that we throw an exception when the callback method is invalid.
   *
   * @covers ::validateBackupOptions
   *
   * @depends testValidateBackupOptions
   */
  public function testValidateBackupOptionsBadMethod() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');

    $this->expectException(InvalidOptionException::class);
    $method->invoke($object, ['callback_method' => '']);
  }

  /**
   * Validate we throw an exception when components are not an array.
   *
   * @covers ::validateBackupOptions
   *
   * @depends testValidateBackupOptions
   */
  public function testValidateBackupOptionsNotArrayComponents() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');

    $this->expectException(InvalidOptionException::class);
    $method->invoke($object, ['components' => '']);
  }

  /**
   * Validate we throw an exception when components have invalid values.
   *
   * @covers ::validateBackupOptions
   *
   * @depends testValidateBackupOptions
   */
  public function testValidateBackupOptionsNotAllowedComponents() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'validateBackupOptions');

    $this->expectException(InvalidOptionException::class);
    $method->invoke($object, ['components' => ['not allowed']]);
  }

  /**
   * Validate that we detect a requireOneOf match.
   *
   * @covers ::requireOneOf
   */
  public function testRequireOneOf() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requireOneOf');

    $this->assertTrue($method->invoke($object, 'match', ['MaTcH']));
  }

  /**
   * Validate that we get an exception when not doing case sensitive matches.
   *
   * @covers ::requireOneOf
   *
   * @depends testRequireOneOf
   */
  public function testRequireOneOfFailCase() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requireOneOf');

    $this->expectException(InvalidOptionException::class);
    $method->invoke($object, 'no match', ['No MaTcH'], FALSE);
  }

  /**
   * Validate we can match by regex pattern.
   *
   * @covers ::requirePatternMatch
   */
  public function testRequirePatternMatch() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requirePatternMatch');
    $this->assertTrue($method->invoke($object, 'match me', '/^match me$/'));
  }

  /**
   * Validate we get an exception with no matter is matched.
   *
   * @covers ::requirePatternMatch
   *
   * @depends testRequirePatternMatch
   */
  public function testRequirePatternMatchFailRegex() {

    $object = $this->getWrappedTrait();
    $method = $this->getInvokableMethod($object, 'requirePatternMatch');

    $this->expectException(InvalidOptionException::class);
    $method->invoke($object, '   match me', '/^match me$/');
  }

  /**
   * Validate we can filter an array to a set of values.
   *
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

    $this->assertEquals(
      ['allowed'],
      $method->invoke($object, $data, ['allowed'])
    );
    $this->assertEquals(
      ['allowed'],
      $method->invoke($object, $data, ['allowed'], TRUE)
    );
    $this->assertEquals(
      [
        'allowed',
        'also allowed',
      ],
      $method->invoke($object, $data, ['allowed', 'also allowed'], TRUE)
    );
  }

  /**
   * Get an anonymous class that wraps the ValidationTrait.
   *
   * @return object
   *   A class using the ValidationTrait.
   */
  protected function getWrappedTrait() {

    $wrapper = new class() {

      use ValidationTrait;

    };

    return $wrapper;
  }

  /**
   * Get a publicly accessible method.
   *
   * Necessary because all validation trait methods are protected, and thus
   * cannot be unit tested directly.
   *
   * @param object $object
   *   The object using the ValidationTrait.
   * @param string $methodName
   *   The method to make accessible for invocation.
   *
   * @return \ReflectionMethod
   *   A wrapper around the method to allow it to be executed.
   *
   * @throws \ReflectionException
   */
  protected function getInvokableMethod($object, $methodName) {

    $ref = new ReflectionObject($object);
    $method = $ref->getMethod($methodName);
    $method->setAccessible(TRUE);

    return $method;
  }

}
