<?php

class Logger {
    
    /**
     * Get the absolute path to the logs directory
     * @return string
     */
    private static function getLogDir() {
        // Go up to project root from src/Helpers/
        return dirname(dirname(__DIR__)) . '/logs/';
    }
    
    /**
     * Log general application events
     * @param string $message Log message
     * @param string $level Log level (INFO, WARNING, ERROR)
     * @param array $context Additional context data
     */
    public static function log($message, $level = 'INFO', $context = []) {
        $logFile = self::getLogDir() . 'app.log';
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[$timestamp] [$level] $message$contextStr\n";
        
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
    
    /**
     * Log admin actions for audit trail
     * @param string $action Action performed (e.g., 'PRODUCT_CREATED', 'USER_DELETED')
     * @param array $details Details about the action
     */
    public static function logAdminAction($action, $details = []) {
        $logFile = self::getLogDir() . 'audit.log';
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? 'anonymous',
            'role' => $_SESSION['role'] ?? 'unknown',
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        @file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
    }
    
    /**
     * Log authentication events (login, logout, failed attempts)
     * @param string $event Event type (LOGIN_SUCCESS, LOGIN_FAILED, LOGOUT)
     * @param string $username Username involved in the event
     * @param array $details Additional details
     */
    public static function logAuth($event, $username, $details = []) {
        $logFile = self::getLogDir() . 'auth.log';
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'username' => $username,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        @file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
    }
    
    /**
     * Log errors
     * @param string $message Error message
     * @param array $context Error context
     */
    public static function error($message, $context = []) {
        self::log($message, 'ERROR', $context);
    }
    
    /**
     * Log warnings
     * @param string $message Warning message
     * @param array $context Warning context
     */
    public static function warning($message, $context = []) {
        self::log($message, 'WARNING', $context);
    }
    
    /**
     * Log info messages
     * @param string $message Info message
     * @param array $context Info context
     */
    public static function info($message, $context = []) {
        self::log($message, 'INFO', $context);
    }
}
