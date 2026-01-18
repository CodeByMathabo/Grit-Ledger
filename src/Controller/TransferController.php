<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionService;
use Exception;

class TransferController
{
    private TransactionService $transactionService;

    public function __construct()
    {
        // wiring up the service layer
        $this->transactionService = new TransactionService();
    }

    // main entry point for the api
    public function handleRequest(): void
    {
        header('Content-Type: application/json');

        try {
            // reading the raw post body
            $input = json_decode(file_get_contents('php://input'), true);

            // basic validation: did they send everything we need?
            if (!isset($input['sourceId'], $input['targetId'], $input['amount'])) {
                throw new Exception("Missing fields: sourceId, targetId, or amount");
            }

            // explicitly casting to ensure we don't pass garbage to the service
            $sourceId = (int) $input['sourceId'];
            $targetId = (int) $input['targetId'];
            $amount = (float) $input['amount'];

            // delegating the hard work to the manager
            $this->transactionService->transferMoney($sourceId, $targetId, $amount);

            // happy path
            echo json_encode([
                'success' => true,
                'message' => 'Transfer completed successfully'
            ]);

        } catch (Exception $e) {
            // generally assuming it's a client error (400) for this demo
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}