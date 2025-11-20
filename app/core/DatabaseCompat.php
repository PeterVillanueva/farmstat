<?php
/**
 * Database Compatibility Layer
 * Provides mysqli compatibility for legacy files
 * PHP 8 Compatible
 * 
 * NOTE: New code should use PDO via Database class
 */

class DatabaseCompat {
    private static ?mysqli $mysqli = null;

    public static function getConnection(): mysqli {
        if (self::$mysqli === null) {
            require_once APP_PATH . '/config/config.php';
            
            self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if (self::$mysqli->connect_error) {
                die("Connection failed: " . self::$mysqli->connect_error);
            }
            
            self::$mysqli->set_charset(DB_CHARSET);
        }
        
        return self::$mysqli;
    }
}

// For backward compatibility with old files
if (!function_exists('getDBConnection')) {
    function getDBConnection(): mysqli {
        return DatabaseCompat::getConnection();
    }
}

