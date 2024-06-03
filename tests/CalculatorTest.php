<?php

namespace Tests;

use App\Calculator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class CalculatorTest.
 */
class CalculatorTest extends TestCase
{

  public static function sumProvider(): Generator
  {
    yield 'zero' => [0, 0, 0];
    yield 'one' => [0, 1, 1];
    yield 'simple' => [1,2,3];
    yield 'negative' => [-12,7,-5];
    yield 'more-negative' => [-5,-4,-9];
    yield 'zero-out' => [-5,0,-5];
  }

  #[DataProvider('sumProvider')]
  public function testItAdds2Values($a, $b, $expected): void
  {
    $calculator = new Calculator();
    $this->assertSame($expected, $calculator->add($a, $b));
  }

  public function testItCreatesAMock()
  {
    $calculator = $this->createMock(Calculator::class);

    $calculator->expects($this->once())
      ->method('add')
      ->with(1, 2)
      ->willReturn(3);

    $calculator->expects($this->never())
      ->method('zero');

    $calculator->add(1, 2);
  }

  public function testItCanCreateAStub()
  {
    $calculator = $this->createStub(Calculator::class);
    $c1 = $this->createMock(Calculator::class);

    $calculator->method('add')
      ->willReturn(3);

    $result = $calculator->add(1, 2);
    $this->assertSame(3, $result);
  }

}
