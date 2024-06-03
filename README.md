# PHPunit <!-- omit in toc -->

- [Overview](#overview)
- [Install](#install)
  - [Settings via `phpunit.xml`](#settings-via-phpunitxml)
- [Usage](#usage)
  - [Data providers](#data-providers)
  - [Testing output](#testing-output)
  - [Disable Deprecation warnings](#disable-deprecation-warnings)
  - [Exceptions](#exceptions)
- [Faking](#faking)
  - [Dummy](#dummy)
  - [Stubs](#stubs)
  - [Mocks](#mocks)
  - [Mocking built-in PHP functions](#mocking-built-in-php-functions)
- [Code coverage](#code-coverage)
  - [XDEBUG](#xdebug)
  - [PCOV](#pcov)
- [Errors](#errors)
  - [Fatal error: Class 'PHPUnit\_Framework\_TestCase' not found in](#fatal-error-class-phpunit_framework_testcase-not-found-in)

## Overview

PHPunit is the preferred package for testing PHP applications.

Homepage: <https://phpunit.de/index.html>

## Install

```bash
composer require --dev phpunit/phpunit
```

### Settings via `phpunit.xml`

- Configure a basic [`phpunit.xml`](./phpunit.xml) to reduce command-line switches.

- `testsuites` are logically grouping of tests. For example: `Unit`, `Functional`.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
</phpunit>
```

## Usage

### Data providers

Data providers define an iterable (array, generator) that passes into the test function.

- Use `PHPUnit\Framework\Attributes\DataProvider` attribute.
- The provider must return an array containing sets if data to pass into the test.
- PHPunit display key name in error message, if applicable.

```php
class CalculateTest extends \PHPUnit\Framework\TestCase;
{
  public static function sumProvider(): array
  {
    return [
      'zero' => [0,0,0],
      'one' => [0,1,1]
    ];
  }

  #[\PHPUnit\Framework\Attributes\DataProvider('sumProvider')]
  public function testItAdds2Values($a = 1, $b = 2, $expected =3): void
  {
    $this->assertSame($expected, $a + $b);
  }
}
```

To pass more than 1 data provider, stack them:

```php
    #[DataProvider('provideAddCases')]
    #[DataProvider('provideNegativeAddCases')]
    public function testItAdds(int $one, int $two, int $expected): void
    {
```

### Testing output

Use `expectOutputString()` to assert expected out (via `echo` or `print`).

```php
public function testExpectFooActualFoo(): void
{
    $this->expectOutputString('foo');

    print 'foo';
}
```

### Disable Deprecation warnings

- Configure Symfony's `Remaining direct deprecation notices` through `ENV` vars.

```bash
SYMFONY_DEPRECATIONS_HELPER=disabled vendor/bin/phpunit
```

```xml
<env name="SYMFONY_DEPRECATIONS_HELPER" value="weak" />
```

- Disable with `SYMFONY_DEPRECATIONS_HELPER=disabled`
- Show count with `SYMFONY_DEPRECATIONS_HELPER=weak`

### Exceptions

To handle exceptions, use a `expectException()` assertion.

```php
class Book {
  public function setTitle(string $title)
  {

  }
}
```

```php
$this->expectException(ArgumentCountError::class);
$book->setTitle();
```

Also available are:

- `expectExceptionCode()`
- `expectExceptionMessage()`
- `expectExceptionMessageMatches()`

## Faking

### Dummy

Dummies are "placeholders". They help when initializing objects and will return NULL on method calls.

```php
$dummy = $this->createMock(SomeClass::class);

// SUT - System Under Test
$sut->action($dummy);
```

### Stubs

Stubs are is an "object doubles" and verify state. Key function is `willReturn()`.

Use them to:

- Test return values.
- Fake return values.

```php
$calculator = $this->createStub(Calculator::class);

$calculator->method('add')
  ->willReturn(3);

$result = $calculator->add(1, 2);
$this->assertSame(3, $result);
```

### Mocks

Use mocks to verify behavior. Key function `expects()`, `shouldBeCalled()`, `shouldBeCalledTimes()`.
Use to:

- Count function calls.
- Track function parameters.
- What value it should return.
- Throw exceptions, if required.

```php
    $calculator = $this->createMock(Calculator::class);

    // `add(1,2)` should be called.
    $calculator->expects($this->once())
      ->method('add')
      ->with(1, 2)
      ->willReturn(3);

    // 'zero()' should NOT be called.
    $calculator->expects($this->never())
      ->method('zero');

    $calculator->add(1, 4);
```

### Mocking built-in PHP functions

For mocking returns or spies: [php-mock](https://github.com/php-mock/php-mock)

- [php-mock/php-mock-phpunit](https://github.com/php-mock/php-mock-phpunit) - PHPUnit integration
- [php-mock/php-mock-mockery](https://github.com/php-mock/php-mock-mockery) - Mockery integration
- [php-mock/php-mock-prophecy](https://github.com/php-mock/php-mock-prophecy) - Prophecy (phpspec) integration

Below, we mock the `ldap_search` function to return false.

```php
$prophet = new PHPProphet();
// Set the namespace our function will be called in.
$prophecy = $prophet->prophesize('Drupal\iq_ldap');
$prophecy->ldap_search(FALSE, 'cn=Users', 'samaccountname=test')->willReturn(FALSE);
$prophecy->reveal();
...
$prophet->checkPredictions();
```

## Code coverage

Code coverage is available via `xdebug` or `pcov`.

Xdebug: Slower, but include path coverage (Xdebug 2.3+).
POC: Faster, but unmaintained, requires patching (PHP8.4+). Line coverage.

@see <https://thephp.cc/articles/pcov-or-xdebug>

<!-- textlint-disable stop-words,write-good -->
If you see a "Warning: XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set":
<!-- textlint-enable stop-words,write-good -->

### XDEBUG

```shell
XDEBUG_MODE=coverage phpunit
```

Note: PHPUnit config will _NOT_ accept this value because it needs to be active earlier in the bootstrap process.
Instead, set the value in a composer script.

```json
  "scripts": {
      "test": "phpunit",
      "test:coverage": [
          "@putenv XDEBUG_MODE=coverage",
          "@test"
      ]
  },
```

### PCOV

1. Install PCOV. If you are using DDEV:

```yml
# .ddev/config.yaml
webimage_extra_packages: ['php${DDEV_PHP_VERSION}-pcov']
```

PCOV will target `src`, `lib`, or `app`. If you require coverage for other directories (`web`, `test`), configure `pcov.directory`.
This also resolves `0%` coverage results.

- Directories should be PSR4 compliant.

- For example. `/etc/php/8.1/cli/conf.d/21-pcov.ini` (PHP8.1)
- To check PHP has loaded the file: `php --ini`

```ini
; 21-pcov.ini
[pcov]
pcov.enabled = 1
pcov.directory = "/var/www/html"
pcov.exclude   = "#/(vendor)/#"
```

To set via CLI:

```shell
php -d pcov.enabled=1 -d pcov.directory=. -dpcov.exclude=\"~vendor~\" vendor/bin/phpunit
```

Or with a composer script:

```json
    "scripts": {
        "test": "phpunit",
        "test:coverage": [
            "php -d pcov.enabled=1 -d pcov.directory=\"/var/www/html\" -d pcov.exclude=\"~vendor~\" vendor/bin/phpunit"
        ]
    }
```

## Errors

### Fatal error: Class 'PHPUnit_Framework_TestCase' not found in

- Older versions of PHPUnit used `extends PHPUnit_Framework_TestCase`
- Newer versions should use `extends \PHPUnit\Framework\TestCase`

```diff
- class ExampleTest extends PHPUnit_Framework_TestCase
+ class ExampleTest extends\PHPUnit\Framework\TestCase
```
