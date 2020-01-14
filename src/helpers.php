<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

use ReflectionMethod;
use TypeError;

function value($value)
{
    return is_callable($value) ? $value() : $value;
}

function call_or_default(?callable $callback, array $args = [], $default = null)
{
    return is_callable($callback)
        ? call_user_func($callback, ...$args)
        : value($default);
}


function callable_or_default($callback, callable $default): callable
{
    return is_callable($callback) ? $callback : $default;
}

function validate_args(): void
{
    [
        'args' => $args,
        'function' => $function,
        'class' => $class,
        'object' => $object,
    ] = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];

    $reflectionMethod = new ReflectionMethod($object, $function);

    $argsIndex = -1;

    foreach (explode(PHP_EOL, $reflectionMethod->getDocComment()) as $docLine) {
        if (preg_match('/@param ((?:(?:[\w?|\\\\<>])+(?:\[])?)+)/', $docLine, $matches)) {
            $argsIndex++;
        }

        if (empty($type = $matches[1] ?? null)) {
            continue;
        }

        validate_arg($args[$argsIndex] ?? null, $type, $class, $function, $argsIndex);
    }
}

function validate_arg($value, string $valueType, string $class, string $function, int $index): void
{
    $mapping = [
        'null' => 'NULL',
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
        'callable' => 'Closure',
    ];

    foreach (explode('|', $valueType) as $type) {
        $type = $mapping[$type] ?? $type;

        if (gettype($value) === $type || $value instanceof $type) {
            return;
        }
    }

    throw new TypeError(vsprintf('Argument %d passed to %s::%s must be of the type %s, %s given', [
        $index + 1,
        $class,
        $function,
        $valueType,
        is_object($value) ? get_class($value) : gettype($value),
    ]));
}
