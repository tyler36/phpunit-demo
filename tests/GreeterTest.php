<?php

use PHPUnit\Framework\TestCase;

class GreeterTest extends TestCase
{
    public function testGreetsWithName(): void
    {
        $greeter = new \App\Greeter;

        $greeting = $greeter->greet('Alice');

        $this->assertSame('Hello, Alice!', $greeting);
    }

    public function testException(): void
    {
        $greeter = new \App\Greeter;

        // Use "expectException" before the code that generates the exception.
        $this->expectException(ArgumentCountError::class);
        $greeter->greet();
    }
}
