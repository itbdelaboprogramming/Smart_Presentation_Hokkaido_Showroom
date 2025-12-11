<?php
/**
 * Simple Environment Variable Loader
 * Loads configuration from .env file
 */

class EnvLoader {
    
    /**
     * Load environment variables from .env file
     * @param string $filePath Path to .env file
     */
    public static function load($filePath = '.env') {
        if (!file_exists($filePath)) {
            // If .env doesn't exist, try to use .env.example as template
            if (file_exists('.env.example')) {
                error_log("Warning: .env file not found. Please copy .env.example to .env and configure it.");
            }
            return;
        }
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set environment variable
                if (!array_key_exists($key, $_ENV)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
    
    /**
     * Get environment variable with fallback
     * @param string $key Variable name
     * @param mixed $default Default value if not found
     * @return mixed Variable value or default
     */
    public static function get($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}
