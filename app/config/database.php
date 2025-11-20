<?php
/**
 * Database Connection Class
 * PHP 8 Compatible - Uses PDO
 */

class Database {
    private static ?PDO $instance = null;
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private string $charset;

    private function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
        $this->charset = DB_CHARSET;
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $db = new self();
            self::$instance = $db->connect();
        }
        return self::$instance;
    }

    private function connect(): PDO {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, $this->username, $this->password, $options);
            return $pdo;
        } catch (PDOException $e) {
            // Try to create database if it doesn't exist
            try {
                $dsn = "mysql:host={$this->host};charset={$this->charset}";
                $tempOptions = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $pdo = new PDO($dsn, $this->username, $this->password, $tempOptions);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->database}`");
                $pdo->exec("USE `{$this->database}`");
                return $pdo;
            } catch (PDOException $e2) {
                error_log("Database connection failed: " . $e2->getMessage());
                die("Database connection failed. Please check your configuration.");
            }
        }
    }

    public static function getConnection(): PDO {
        return self::getInstance();
    }
}

