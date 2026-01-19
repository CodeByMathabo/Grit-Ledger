<?php

declare(strict_types=1);

use App\Controller\TransferController;

// bring in the autoloader so we don't have to include files manually
require_once __DIR__ . '/../vendor/autoload.php';

// this is the main entry point for all api traffic
try {
    // wiring up the controller
    $controller = new TransferController();

    // passing the baton to the controller to handle the logic
    $controller->handleRequest();

} catch (Exception $e) {
    // catch-all for anything that slips through the cracks
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
}