<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\DatabaseConnection;
use App\Model\Account;
use PDO;
use RuntimeException;

class AccountRepository
{
    private PDO $pdo;

    public function __construct()
    {
        // getting the connection from our singleton
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function findById(int $id): ?Account
    {
        $stmt = $this->pdo->prepare("SELECT * FROM accounts WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapToAccount($row);
    }

    // locking the row for update is crucial for atomic transactions
    public function findByIdForUpdate(int $id): ?Account
    {
        $stmt = $this->pdo->prepare("SELECT * FROM accounts WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapToAccount($row);
    }

    public function updateBalance(int $id, float $newBalance): void
    {
        $stmt = $this->pdo->prepare("UPDATE accounts SET balance = :balance WHERE id = :id");
        $stmt->execute([
            'balance' => $newBalance,
            'id' => $id
        ]);
    }

    // helper to keep the fetch methods clean
    private function mapToAccount(array $row): Account
    {
        return new Account(
            (int) $row['id'],
            $row['account_number'],
            $row['account_holder_name'],
            (float) $row['balance']
        );
    }
}