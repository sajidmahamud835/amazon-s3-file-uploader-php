<?php

namespace App;

use Dotenv\Dotenv;

class Config
{
    private static array $env = [];

    public static function load(string $path): void
    {
        $dotenv = Dotenv::createImmutable($path);
        // Safely load and catch exception if .env is missing?
        // For now, let it throw or just use safeLoad()
        $dotenv->safeLoad();
        self::$env = $_ENV;
    }

    public static function get(string $key, $default = null)
    {
        return self::$env[$key] ?? $default;
    }

    public static function require(string $key): string
    {
        if (!isset(self::$env[$key])) {
            throw new \RuntimeException("Missing configuration for key: $key");
        }
        return self::$env[$key];
    }
}
