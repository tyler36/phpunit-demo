<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase {

  public function testExpectFooActualFoo(): void
  {
      $this->expectOutputString('foo');
      print 'foo';
  }
}
