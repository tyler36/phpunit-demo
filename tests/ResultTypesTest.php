<?php
namespace Tests;
use PHPUnit\Framework\TestCase;

/**
 * Class ResultTypesTest.
 */
class ResultTypesTest extends TestCase
{

  public function test_it_passes()
  {
    self::assertIsString('hello');
  }

  public function test_it_fails()
  {
    self::assertIsString(true);
  }

  public function test_it_generates_an_error()
  {
    self::assertIsString('hello');
    $app = new \MissingClass();
  }

  public function test_it_generates_a_warning()
  {
    self::assertTrue(true);
    trigger_error('non-fatal error was triggered', E_USER_WARNING);
  }

  public function test_it_is_risky()
  {
    $a = 5 + 4;
  }

  public function test_it_trigger_deprecation()
  {
    self::assertTrue(true);
    trigger_error('example deprecation', E_USER_DEPRECATED);
  }

  public function test_it_triggers_notice()
  {
    self::assertTrue(true);
    trigger_error('example notice', E_USER_NOTICE);
  }

  public function test_mark_incomplete()
  {
    $this->markTestIncomplete('// TODO: mark incomplete');
  }

  public function test_mark_skipped()
  {
    $this->markTestSkipped('// TODO: mark skipped');
  }

}
