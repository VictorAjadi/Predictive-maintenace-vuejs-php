<?php
// Set CORS headers to allow requests from your frontend (localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // This is crucial for allowing credentials
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session
session_start();

// Debug: Check if session has been started
if (session_status() === PHP_SESSION_ACTIVE) {
    // Check if a session exists and return the session ID if it does
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            "status" => "success",
            "session_id" => session_id(),  // Return the session ID if logged in
            "user_id" => $_SESSION['user_id'],
            "email" => $_SESSION['email']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No active session found."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Session not started."
    ]);
}
?>
