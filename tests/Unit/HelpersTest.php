<?php declare(strict_types=1);

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

    /**
     * @dataProvider validateArgsErrorProvider
     */
    public function test_validate_args_error(string $method, string $type, int $index, array $args): void
    {
        $className = ValidateArgsClass::class;

        $this->expectExceptionMessage("Argument {$index} passed to {$className}::{$method} must be of the type {$type} given");

        (new ValidateArgsClass)->{$method}(...$args);
    }

    public function validateArgsErrorProvider(): array
    {
        return [
            ['string', 'string, stdClass', 1, [new \stdClass]],
            ['string', 'string, integer', 1, [1]],
            ['object', '\DmitryBubyakin\NovaMedialibraryField\Tests\Unit\ValidateArgsClass, integer', 1, [1]],
            ['object', '\DmitryBubyakin\NovaMedialibraryField\Tests\Unit\ValidateArgsClass, array', 1, [[]]],
            ['mixed', 'string|callable|\DmitryBubyakin\NovaMedialibraryField\Tests\Unit\ValidateArgsClass, double', 1, [1.0]],
            ['mixed', 'string|callable|\DmitryBubyakin\NovaMedialibraryField\Tests\Unit\ValidateArgsClass, boolean', 1, [true]],
            ['multiple', 'string, integer', 1, [1, 2, 3, 1]],
            ['multiple', '\stdClass, integer', 2, ['string', 2, 3, 1]],
            ['multiple', 'float|int|bool, string', 3, ['string', new \stdClass, 'string', 1]],
            ['multiple', 'float|int|bool, stdClass', 3, ['string', new \stdClass, new \stdClass, 1]],
            ['multiple', 'object|null, integer', 4, ['string', new \stdClass, true, 1]],
            ['multiple', 'object|null, boolean', 4, ['string', new \stdClass, true, true]],
        ];
    }

    /**
     * @dataProvider validateArgsProvider
     */
    public function test_validate_args(string $method, array $args): void
    {
        (new ValidateArgsClass)->{$method}(...$args);

        $this->assertTrue(true);
    }

    public function validateArgsProvider(): array
    {
        return [
            ['string', ['string']],
            ['object', [new ValidateArgsClass]],
            ['mixed', ['string']],
            ['mixed', [ValidateArgsClass::callable()]],
            ['mixed', [new ValidateArgsClass]],
            ['multiple', ['string', new \stdClass, 1.0, new \stdClass]],
            ['multiple', ['string', new \stdClass, 1, new \stdClass]],
            ['multiple', ['string', new \stdClass, true, new \stdClass]],
            ['multiple', ['string', new \stdClass, true, null]],
            ['multiple', ['string', new \stdClass, true]],
        ];
    }
}

class ValidateArgsClass
{
    public static function callable(): callable
    {
        return function (): void {
            //
        };
    }

    /**
     * @param string $value
     */
    public function string($value): void
    {
        validate_args();
    }

    /**
     * @param \DmitryBubyakin\NovaMedialibraryField\Tests\Unit\ValidateArgsClass $value
     */
    public function object($value): void
    {
        validate_args();
    }

    /**
     * @param string|callable|\DmitryBubyakin\NovaMedialibraryField\Tests\Unit\ValidateArgsClass $value
     */
    public function mixed($value): void
    {
        validate_args();
    }

    /**
     * @param string $one
     * @param \stdClass $two
     * @param float|int|bool $three
     * @param object|null $four
     */
    public function multiple($one, $two, $three, $four = null): void
    {
        validate_args();
    }
}
