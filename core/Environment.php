<?php

namespace Koru;

class Environment
{
    private static ?string $environment = null;
    
    public static function detect(): string
    {
        if (self::$environment === null) {
            $env = Config::get('APP_ENV', 'production');
            
            // Güvenli string dönüşümü
            $env = is_string($env) ? strtolower(trim($env)) : 'production';
            
            // Environment mapping
            $environmentMap = [
                'dev' => 'development',
                'develop' => 'development', 
                'development' => 'development',
                'local' => 'development',
                'stage' => 'staging',
                'staging' => 'staging',
                'test' => 'testing',
                'testing' => 'testing',
                'prod' => 'production',
                'production' => 'production'
            ];
            
            self::$environment = $environmentMap[$env] ?? 'production';
        }
        
        return self::$environment;
    }
    
    public static function isDevelopment(): bool
    {
        return self::get() === 'development';
    }
    
    public static function isProduction(): bool
    {
        return self::get() === 'production';
    }
    
    public static function isStaging(): bool
    {
        return self::get() === 'staging';
    }
    
    public static function isTesting(): bool
    {
        return self::get() === 'testing';
    }
    
    public static function isDebugging(): bool
    {
        $debug = Config::get('APP_DEBUG', false);
        
        if (is_string($debug)) {
            return in_array(strtolower(trim($debug)), ['true', '1', 'yes', 'on'], true);
        }
        
        return (bool) $debug;
    }
    
    public static function get(): string
    {
        if (self::$environment === null) {
            self::detect();
        }
        
        return self::$environment;
    }
    
    // Cache'i temizle (test için)
    public static function reset(): void
    {
        self::$environment = null;
    }
}