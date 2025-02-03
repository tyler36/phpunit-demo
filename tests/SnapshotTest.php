<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class SnapshotTest.
 */
class SnapshotTest extends TestCase
{
  use MatchesSnapshots;

  public function test_it_is_foo() {
      $this->assertMatchesSnapshot('foo');
  }
}
