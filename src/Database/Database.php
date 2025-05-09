<?php

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;
    private static ?array $config = null;

    private function __construct()
    {
    }

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            if (self::$config === null) {
                throw new RuntimeException('Database configuration not set');
            }

            try {
                self::$instance = new PDO(
                    "mysql:host=" . self::$config['host'] . ";dbname=" . self::$config['dbname'] . ";charset=utf8mb4",
                    self::$config['username'],
                    self::$config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                throw new RuntimeException("Connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}