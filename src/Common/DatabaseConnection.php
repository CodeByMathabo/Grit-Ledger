<?php

declare(strict_types=1);

namespace App\Common;

use PDO;
use PDOException;
use RuntimeException;

// classic singleton to save connections
class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private ?PDO $pdo = null;

    private const DB_HOST = 'localhost';
    private const DB_NAME = 'grit_ledger';
    private const DB_USER = 'root';
    private const DB_PASS = '';

    private function __construct()
    {
        try {
            $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS);

            // strict error mode is better for debugging
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // safety first: prevent SQL injection
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
            // hide credentials from the stack trace
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): DatabaseConnection
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}