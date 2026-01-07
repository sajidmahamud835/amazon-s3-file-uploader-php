<?php

namespace App;

use Dotenv\Dotenv;

class Config
{
    private static array $env = [];
    private static bool $demoMode = false;

    public static function load(string $path): void
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->safeLoad();
        self::$env = $_ENV;
    }

    public static function get(string $key, $default = null)
    {
        // Check session first (demo mode), then environment
        if (self::$demoMode && isset($_SESSION['aws_credentials'][$key])) {
            return $_SESSION['aws_credentials'][$key];
        }
        return self::$env[$key] ?? $default;
    }

    public static function require(string $key): string
    {
        $value = self::get($key);
        if (empty($value)) {
            throw new \RuntimeException("Missing configuration for key: $key");
        }
        return $value;
    }

    /**
     * Check if all required AWS credentials are configured (env or session).
     */
    public static function hasAwsCredentials(): bool
    {
        $required = ['AWS_BUCKET', 'AWS_REGION', 'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY'];
        foreach ($required as $key) {
            if (empty(self::get($key))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Store user-provided credentials in session for demo mode.
     */
    public static function setSessionCredentials(array $credentials): void
    {
        $_SESSION['aws_credentials'] = [
            'AWS_BUCKET' => $credentials['bucket'] ?? '',
            'AWS_REGION' => $credentials['region'] ?? 'us-east-1',
            'AWS_ACCESS_KEY_ID' => $credentials['access_key'] ?? '',
            'AWS_SECRET_ACCESS_KEY' => $credentials['secret_key'] ?? '',
        ];
        self::$demoMode = true;
    }

    /**
     * Enable demo mode (use session credentials).
     */
    public static function enableDemoMode(): void
    {
        self::$demoMode = true;
    }

    /**
     * Check if we are in demo mode (using session credentials).
     */
    public static function isDemoMode(): bool
    {
        return !empty($_SESSION['aws_credentials']);
    }

    /**
     * Clear demo mode session credentials.
     */
    public static function clearSession(): void
    {
        unset($_SESSION['aws_credentials']);
        self::$demoMode = false;
    }
}
