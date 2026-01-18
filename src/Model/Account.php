<?php

declare(strict_types=1);

namespace App\Model;

class Account
{
    // standard oop encapsulation
    private int $id;
    private string $accountNumber;
    private string $accountHolderName;
    private float $balance;

    public function __construct(int $id, string $accountNumber, string $accountHolderName, float $balance)
    {
        $this->id = $id;
        $this->accountNumber = $accountNumber;
        $this->accountHolderName = $accountHolderName;
        $this->balance = $balance;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getAccountHolderName(): string
    {
        return $this->accountHolderName;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    // only allowing balance updates for now
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
}