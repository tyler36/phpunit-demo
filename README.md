# PHPunit <!-- omit in toc -->

- [Overview](#overview)
- [Install](#install)
  - [Settings via `phpunit.xml`](#settings-via-phpunitxml)
- [Outcomes](#outcomes)
  - [Successful test](#successful-test)
  - [Failure](#failure)
  - [Error](#error)
  - [Warning](#warning)
  - [Risky](#risky)
  - [Deprecated](#deprecated)
  - [Notice](#notice)
  - [Incomplete](#incomplete)
  - [Skipped](#skipped)
- [Usage](#usage)
  - [Assertions](#assertions)
  - [Expecting Exceptions](#expecting-exceptions)
  - [Metadata](#metadata)
  - [Disable Deprecation warnings](#disable-deprecation-warnings)
  - [Data providers](#data-providers)
  - [Testing output](#testing-output)
    - [Marking output as risky](#marking-output-as-risky)
  - [Test Doubles](#test-doubles)
    - [Dummy](#dummy)
    - [Stubs](#stubs)
    - [Mocks](#mocks)
  - [Mocking built-in PHP functions](#mocking-built-in-php-functions)
- [Code coverage](#code-coverage)
  - [XDEBUG](#xdebug)
  - [PCOV](#pcov)
  - [Risky coverage](#risky-coverage)
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

## Outcomes

CLI lists outcomes as followed:

- `.`: Successful test with no issues.
- `F`: Failure; assertion fails when running test method.
- `E`: Error; an error or unexpected exception occurred.
- `W`: Warning.
- `R`: Risky.
- `D`: Deprecation.
- `N`: Notice.
- `I`: Incomplete.
- `S`: Skipped.

Use `--stop-on-defect` to stop as soon as the runner encounters a non-passing test.
Each non-passing type also has a `--stop-on-{outcome}` argument to target a specific outcome type.
This is available as a configuration option (`stopOn{outcome}`).

For non-fatal outcomes, a `failOn{Outcome}` configuration setting is available.

### Successful test

A test makes an assertion which is **true**.

```php
self::assertIsString('hello');
```

### Failure

A test makes an assertion which is **false**.
Failures imply the code is not working as expected or contains a bug.

```php
self::assertIsString(true);
```

### Error

Test encounters a PHP error or unexpected exception.
Errors signify the code is non-functional and invalid.
For example:

- Missing classes.
- Invalid types.
- Unexpected exception.

```php
$app = new \MissingClass();
```

@see [Expecting Exceptions](#expecting-exceptions)

### Warning

Triggered when a non-fatal error, runtime warning (`E_WARNING`), occurs.

```php
self::assertTrue(true);
trigger_error('non-fatal error was triggered', E_USER_WARNING);
```

### Risky

[Risky tests](https://docs.phpunit.de/en/10.5/risky-tests.html#risky-tests) include:

- Useless test, tests that do NOT perform assertions.
  - Disable via `--dont-report-useless-tests` CLI
  - Enable via `beStrictAboutTestsThatDoNotTestAnything="false"` config.
- Unintentional code coverage. @see [Code coverage](#code-coverage)
- Output during tests. @see [Testing output](#testing-output)
- Test execution timeout. @see [Test Execution Timeout](https://docs.phpunit.de/en/10.5/risky-tests.html#test-execution-timeout)
- Global state manipulation
  - Enable via `--strict-global-state` CLI
  - Enable via `beStrictAboutChangesToGlobalState="true"` config.

Target with:

- `--stop-on-defect`
- `--stop-on-risky`

### Deprecated

Triggered when test encounters a deprecation; `E_DEPRECATED`, `E_USER_DEPRECATED`, or PHPUnit deprecation.

```php
trigger_error('example deprecation', E_USER_DEPRECATED);
```

Target with:

- `--stop-on-defect`
- `--stop-on-deprecation`

@see [Disable Deprecation warnings](#disable-deprecation-warnings)

### Notice

Triggered when test encounters a notice; `E_STRICT`, `E_NOTICE`, or `E_USER_NOTICE`.

```php
trigger_error('non-fatal error was triggered', E_USER_WARNING);
```

### Incomplete

Using `$this->markTestIncomplete('This test has not been implemented yet.');` serves as a placeholder.
The test, in its current state, is not meant to pass or failure.

```php
$this->markTestIncomplete('// TODO: mark incomplete');
```

### Skipped

[Skipped tests](https://docs.phpunit.de/en/10.5/writing-tests-for-phpunit.html#skipping-tests) are tests that are not required.
For example:

- Tests for Mysql in a Postgres environment.
- Tests not designed to work in CI or locally.
- Tests not without require PHP extension.

To dynamically skip tests, use the following attributes:

- `RequiresFunction(string $functionName)`
- `RequiresMethod(string $className, string $functionName)`
- `RequiresOperatingSystem(string $regularExpression)`
- `RequiresOperatingSystemFamily(string $operatingSystemFamily)`
- `RequiresPhp(string $versionRequirement)`
- `RequiresPhpExtension(string $extension, ?string $versionRequirement)`
- `RequiresPhpunit(string $versionRequirement)`
- `RequiresSetting(string $setting, string $value)`

## Usage

### Assertions

### Expecting Exceptions

To handle exceptions, use a `expectException()` assertion.

```php
class Book {
  public function setTitle(string $title){}
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

### Metadata

PHPUnit 10+ embraces PHP attributes, instead of doc-block comments.
@see <https://docs.phpunit.de/en/11.2/attributes.html>.

- Doc-block metadata.

```php
/**
 * Class CalculatorTest.
 *
 * @coversDefaultClass \App\Calculator
 * @group math
 */
class CalculatorTest extends TestCase
```

- PHP attributes.

```php
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
/**
 * Class CalculatorTest.
 */
#[CoversClass(\App\Calculator::class)]
#[Group('math')]
class CalculatorTest extends TestCase
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

#### Marking output as risky

PHPUnit can be strict about output during tests. Enable this via:

- `--disallow-test-output` CLI
- `beStrictAboutOutputDuringTests="true"` config.

### Test Doubles

#### Dummy

Dummies are "placeholders". They help when initializing objects and will return NULL on method calls.

```php
$dummy = $this->createMock(SomeClass::class);

// SUT - System Under Test
$sut->action($dummy);
```

#### Stubs

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

#### Mocks

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

Code coverage is a metric for test coverage of code.
Code coverage is available via `xdebug` or `pcov`:

- Xdebug: Slower, but include path coverage (Xdebug 2.3+).
- POC: Faster, but unmaintained, requires patching (PHP8.4+). Line coverage.

@see <https://thephp.cc/articles/pcov-or-xdebug>

Note:
You will see the following warning when trying to generate coverage reports without xdebug/pcov loaded.

<!-- textlint-disable stop-words,write-good -->
If you see a "Warning: XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set":
<!-- textlint-enable stop-words,write-good -->

Use `CoversClass` attribute to target unit of code for coverage.

```php
#[CoversClass(Calculator::class)]
class CalculatorTest extends TestCase {...}
```

Set `low`/`high` coverage limits in `phpunit.dist.xml`.

```xml
<html outputDirectory="logs/php-coverage/html-coverage" lowUpperBound="50" highLowerBound="90" />
```

### XDEBUG

CLI:

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
          "@test --coverage-text"
      ]
  },
```

### PCOV

[PCOV homepage](https://github.com/krakjoe/pcov)

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
php -d pcov.enabled=1 -d pcov.directory=. -d pcov.exclude=\"~vendor~\" vendor/bin/phpunit --coverage-text
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

### Risky coverage

PHPunit can mark tests as `Risky` if they have unintentional code coverage.

- Enable via `--strict-coverage` CLI
- Enable via `beStrictAboutCoverageMetadata="true"` config.

## Errors

### Fatal error: Class 'PHPUnit_Framework_TestCase' not found in

- Older versions of PHPUnit used `extends PHPUnit_Framework_TestCase`
- Newer versions should use `extends \PHPUnit\Framework\TestCase`

```diff
- class ExampleTest extends PHPUnit_Framework_TestCase
+ class ExampleTest extends\PHPUnit\Framework\TestCase
```
