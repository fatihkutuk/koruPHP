<?php

namespace Koru;

class Config
{
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
    
    public static function set(string $key, $value): void
    {
        $_ENV[$key] = $value;
    }
}