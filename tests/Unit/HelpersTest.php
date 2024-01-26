<?php

declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField\Tests\Unit;

use function DmitryBubyakin\NovaMedialibraryField\call_or_default;
use function DmitryBubyakin\NovaMedialibraryField\callable_or_default;
use function DmitryBubyakin\NovaMedialibraryField\validate_args;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function test_call_or_default(): void
    {
        $this->assertSame(1, call_or_default(null, [], 1));

        $this->assertSame(2, call_or_default(function () {
            return 2;
        }, [], 1));

        $this->assertSame(2, call_or_default(function () {
            return 2;
        }));
    }

    /** @test */
    public function test_callable_or_default(): void
    {
        $this->assertTrue(is_callable(callable_or_default(null, function (): void {
            //
        })));

        $this->assertTrue(is_callable(callable_or_default('string', function (): void {
            //
        })));
    }
}