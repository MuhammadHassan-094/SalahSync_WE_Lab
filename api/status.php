<?php
// Basic API status endpoint
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple status response
$response = [
    'code' => 200,
    'message' => 'API is online',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_time' => time()
];

// Send JSON response
echo json_encode($response);
?> 