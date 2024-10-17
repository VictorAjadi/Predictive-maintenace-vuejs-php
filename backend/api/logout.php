<?php

// Set CORS headers to allow requests from your frontend (localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
include '../db.php'; // Adjust the path if needed

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start session
    session_start();

    // Destroy the session
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session

    // Send success response
    echo json_encode([
        "status" => "success",
        "message" => "Logged out successfully."
    ]);
} else {
    // If the request is not POST, return an error message
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>
