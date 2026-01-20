<?php

declare(strict_types=1);

namespace App\Common;

use PDO;
use PDOException;
use RuntimeException;

class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        // Why: Load config from Environment Variables (injected by Docker) to avoid hardcoding.
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'grit_ledger';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        try {
            // Why: Logging the connection attempt helps debug if the container can't reach the DB.
            error_log("Attempting to connect to database at: " . $host);

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $db);

            $this->pdo = new PDO($dsn, $user, $pass);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            error_log("Database connection established successfully.");

        } catch (PDOException $e) {
            // Why: Log the specific error to stderr so it shows up in Docker logs.
            error_log("Database Connection Error: " . $e->getMessage());
            throw new RuntimeException("Database connection failed. Check logs for details.");
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