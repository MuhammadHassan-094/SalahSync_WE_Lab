<?php
// Check if debug mode is requested
$debug = isset($_GET['debug']) && $_GET['debug'] === 'true';

// If in debug mode, enable full error reporting and display
if ($debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    // Set content type to plain text for better error readability
    header("Content-Type: text/plain");
    echo "=== DEBUG MODE ENABLED ===\n\n";
    echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
    echo "GET Parameters: " . json_encode($_GET) . "\n";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "Raw POST Body: " . file_get_contents('php://input') . "\n";
    }
    echo "\n=== EXECUTION LOG ===\n\n";
} else {
    // Disable all error output to prevent HTML in the response
    ini_set('display_errors', 0);
    error_reporting(0);
    
    // Set headers for CORS and JSON response
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json");
}

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Response structure
$response = [
    'code' => 500,
    'message' => 'Internal Server Error',
    'data' => null
];

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Debug log function
function debug_log($message) {
    global $debug;
    if ($debug) {
        if (is_array($message) || is_object($message)) {
            echo json_encode($message, JSON_PRETTY_PRINT) . "\n";
        } else {
            echo $message . "\n";
        }
    }
}

// Special debug mode to test database connection
if ($action === 'test_db') {
    try {
        debug_log("Testing database connection");
        
        // Include database file
        include_once '../db/database.php';
        debug_log("Database file included");
        
        // Database connection check
        $db = new Database();
        debug_log("Database object created");
        
        $conn = $db->getConnection();
        debug_log("Connection obtained");
        
        // Test if connection is working
        $result = $conn->query("SELECT 1 as test");
        $testRow = $result->fetch_assoc();
        debug_log("Query executed and fetched");
        
        $response = [
            'code' => 200,
            'message' => 'Database connection successful',
            'data' => [
                'connection_status' => ($testRow && isset($testRow['test'])) ? 'OK' : 'Error',
                'database_info' => [
                    'host' => $conn->host_info,
                    'server_version' => $conn->server_info,
                    'server_stats' => $conn->stat,
                ]
            ]
        ];
        debug_log("Response prepared: " . json_encode($response));
    } catch (Exception $e) {
        debug_log("Database connection error: " . $e->getMessage());
        $response = [
            'code' => 500,
            'message' => 'Database connection error: ' . $e->getMessage(),
            'data' => null
        ];
    }
    
    // Send JSON response if not in debug mode
    if (!$debug) {
        echo json_encode($response);
    } else {
        debug_log("Final response: " . json_encode($response, JSON_PRETTY_PRINT));
    }
    exit;
}

// Main API logic
try {
    debug_log("Starting main API logic");
    
    // Include database file
    include_once '../db/database.php';
    debug_log("Database file included");
    
    $db = new Database();
    debug_log("Database object created");
    
    $conn = $db->getConnection();
    debug_log("Database connection established");

    // Handle GET requests (Fetching prayer data)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        debug_log("Processing GET request");
        
        $userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        $detail = isset($_GET['detail']) ? (bool)$_GET['detail'] : false;
        
        debug_log("Parameters: userId=$userId, startDate=$startDate, endDate=$endDate, detail=$detail");

        if (!$userId || !$startDate || !$endDate) {
            debug_log("Missing required parameters");
            $response['code'] = 400;
            $response['message'] = 'Missing required parameters: user_id, start_date, end_date';
        } else {
            // If detail is requested, return full prayer details for the period
            if ($detail) {
                debug_log("Getting detailed prayer data");
                $stmt = $conn->prepare("
                    SELECT prayer_id, status, date 
                    FROM prayers 
                    WHERE user_id = ? AND date BETWEEN ? AND ?
                ");
                $stmt->bind_param('sss', $userId, $startDate, $endDate);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $prayers = [];
                while ($row = $result->fetch_assoc()) {
                    $prayers[] = $row;
                }
                
                debug_log("Found " . count($prayers) . " prayer records");
                
                $response['code'] = 200;
                $response['message'] = 'Prayer details retrieved successfully';
                $response['data'] = [
                    'prayers' => $prayers
                ];
            } 
            // Otherwise, return summary stats for the period
            else {
                debug_log("Getting prayer summary stats");
                $stmt = $conn->prepare("
                    SELECT COUNT(*) as completed
                    FROM prayers 
                    WHERE user_id = ? AND date BETWEEN ? AND ? AND status = 'completed'
                ");
                $stmt->bind_param('sss', $userId, $startDate, $endDate);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                debug_log("Completed prayers: " . $row['completed']);
                
                $response['code'] = 200;
                $response['message'] = 'Prayer stats retrieved successfully';
                $response['data'] = [
                    'completed' => (int)$row['completed']
                ];
            }
        }
    }
    // Handle POST requests (Creating/Updating prayer status)
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        debug_log("Processing POST request");
        
        // Get JSON data from request body
        $inputJSON = file_get_contents('php://input');
        debug_log("Raw input: " . $inputJSON);
        
        $data = json_decode($inputJSON, true);
        debug_log("Decoded data: " . json_encode($data));
        
        // Check required fields
        if (!isset($data['user_id']) || !isset($data['prayer_id']) || !isset($data['status']) || !isset($data['date'])) {
            debug_log("Missing required fields");
            $response['code'] = 400;
            $response['message'] = 'Missing required fields: user_id, prayer_id, status, date';
        } else {
            // Validate status
            if (!in_array($data['status'], ['pending', 'completed'])) {
                debug_log("Invalid status value: " . $data['status']);
                $response['code'] = 400;
                $response['message'] = 'Invalid status value. Must be "pending" or "completed".';
            } else {
                debug_log("Valid prayer status update request");
                
                // Check if the prayer entry already exists
                $stmt = $conn->prepare("
                    SELECT id FROM prayers 
                    WHERE user_id = ? AND prayer_id = ? AND date = ?
                ");
                $stmt->bind_param('sss', $data['user_id'], $data['prayer_id'], $data['date']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing prayer entry
                    $row = $result->fetch_assoc();
                    $id = $row['id'];
                    
                    debug_log("Updating existing prayer entry with ID: " . $id);
                    
                    $stmt = $conn->prepare("
                        UPDATE prayers 
                        SET status = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->bind_param('si', $data['status'], $id);
                    $stmt->execute();
                    
                    $response['code'] = 200;
                    $response['message'] = 'Prayer status updated';
                    $response['data'] = [
                        'id' => $id,
                        'updated' => true
                    ];
                } else {
                    // Create new prayer entry
                    debug_log("Creating new prayer entry");
                    
                    $stmt = $conn->prepare("
                        INSERT INTO prayers (user_id, prayer_id, status, date, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, NOW(), NOW())
                    ");
                    $stmt->bind_param('ssss', $data['user_id'], $data['prayer_id'], $data['status'], $data['date']);
                    $stmt->execute();
                    
                    $newId = $conn->insert_id;
                    debug_log("New prayer entry created with ID: " . $newId);
                    
                    $response['code'] = 201;
                    $response['message'] = 'Prayer status created';
                    $response['data'] = [
                        'id' => $newId,
                        'created' => true
                    ];
                }
            }
        }
    } else {
        debug_log("Unsupported method: " . $_SERVER['REQUEST_METHOD']);
        $response['code'] = 405;
        $response['message'] = 'Method not allowed';
    }
} catch (Exception $e) {
    debug_log("Exception caught: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
    $response['code'] = 500;
    $response['message'] = 'Server error: ' . $e->getMessage();
}

// Send JSON response if not in debug mode
if (!$debug) {
    echo json_encode($response);
} else {
    debug_log("\n=== FINAL RESPONSE ===\n");
    debug_log($response);
} 