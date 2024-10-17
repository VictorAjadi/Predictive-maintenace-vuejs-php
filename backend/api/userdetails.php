<?php
// Set CORS headers to allow requests from your frontend (localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Access-Control-Allow-Credentials: true'); // Allows credentials to be sent
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'GET, OPTIONS') {
    http_response_code(200);
    exit();
}
include '../db.php'; // Adjust the path if needed

// Start the session to access session variables
session_start();

// Check if the user is logged in by verifying if the session variables exist
if (isset($_SESSION['user_id']) && isset($_SESSION['email'])) {
    // If session is valid, return user details
    echo json_encode([
        "status" => "success",
        "user" => [
            "id" => $_SESSION['user_id'],
            "username" => isset($_SESSION['username']) ? $_SESSION['username'] : null, // Check if username exists
            "email" => $_SESSION['email']
        ]
    ]);
} else {
    // If no valid session exists, return an error message
    echo json_encode([
        "status" => "error",
        "message" => "User is not authenticated."
    ]);
}
?>
