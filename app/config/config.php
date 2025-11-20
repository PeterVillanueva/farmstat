<?php
/**
 * Application Configuration
 * PHP 8 Compatible
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug mode
define('APP_DEBUG', true);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application paths
define('APP_ROOT', dirname(__DIR__, 2));
define('APP_PATH', APP_ROOT . '/app');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('MODELS_PATH', APP_PATH . '/models');
define('VIEWS_PATH', APP_PATH . '/views');
define('ASSETS_PATH', APP_ROOT . '/assets');

// Database configuration
define('DB_HOST', '127.0.0.1:3325');
define('DB_NAME', 'farmstats_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'FarmStat');

// Determine base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $scriptName === '/' ? '' : $scriptName;
define('BASE_URL', $baseUrl);
define('APP_URL', $protocol . '://' . $host . $baseUrl);

// Timezone
date_default_timezone_set('Asia/Manila');

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        MODELS_PATH . '/' . $class . '.php',
        CONTROLLERS_PATH . '/' . $class . '.php',
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/helpers/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load helper functions
$helpersPath = APP_ROOT . '/app/helpers/helpers.php';
if (file_exists($helpersPath)) {
    require_once $helpersPath;
}

