<?php
// Basic test script to check API access
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Create a response with essential server information
$response = [
    'status' => 'success',
    'message' => 'API test endpoint is accessible',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'script_filename' => $_SERVER['SCRIPT_FILENAME'],
    ]
];

// Output the response
echo json_encode($response);
?> 