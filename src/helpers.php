<?php declare(strict_types=1);

namespace DmitryBubyakin\NovaMedialibraryField;

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