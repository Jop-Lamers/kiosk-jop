<?php
// API Configuration
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

// Helper function to respond with JSON
function apiResponse($success, $data = null, $message = null, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

// Helper function to get JSON input
function getJsonInput()
{
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

// Helper function to validate required fields
function validateRequired($data, $fields)
{
    foreach ($fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            return false;
        }
    }
    return true;
}

// Database error handler
function handleDbError($error)
{
    apiResponse(false, null, "Database error: " . htmlspecialchars($error), 500);
}
