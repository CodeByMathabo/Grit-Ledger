<?php

declare(strict_types=1);

namespace App\Service;

use App\Common\DatabaseConnection;
use App\Repository\AccountRepository;
use Exception;
use PDO;
use RuntimeException;

class TransactionService
{
    private AccountRepository $accountRepository;
    private PDO $pdo;

    public function __construct()
    {
        // standard manual injection
        $this->accountRepository = new AccountRepository();
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function transferMoney(int $sourceId, int $targetId, float $amount): void
    {
        // basic validation first
        if ($amount <= 0) {
            throw new RuntimeException("Amount must be greater than zero");
        }

        try {
            // start the transaction to ensure atomicity
            $this->pdo->beginTransaction();

            // fetch and lock both accounts so no one else can touch them
            $sourceAccount = $this->accountRepository->findByIdForUpdate($sourceId);
            $targetAccount = $this->accountRepository->findByIdForUpdate($targetId);

            if ($sourceAccount === null || $targetAccount === null) {
                throw new RuntimeException("Invalid account ID provided");
            }

            // check if they have enough cash
            if ($sourceAccount->getBalance() < $amount) {
                throw new RuntimeException("Insufficient funds for transfer");
            }

            // calculate new balances
            $newSourceBalance = $sourceAccount->getBalance() - $amount;
            $newTargetBalance = $targetAccount->getBalance() + $amount;

            // persist changes to db
            $this->accountRepository->updateBalance($sourceId, $newSourceBalance);
            $this->accountRepository->updateBalance($targetId, $newTargetBalance);

            // if we made it here, save everything
            $this->pdo->commit();

        } catch (Exception $e) {
            // if anything crashes, undo all database changes
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}