<?php

namespace App;

/**
 * Class Calculator.
 */
class Calculator
{
  public function add(int $a, int $b): int
  {
    return $a + $b;
  }

  public function zero(): int {
    return 0;
  }
}
