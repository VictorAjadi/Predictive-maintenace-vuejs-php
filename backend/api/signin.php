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

include '../db.php'; // Adjust the path if needed

// Start session
session_start();

// Get the input data (email and password) from the request body
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$password = $data['password'];

// Prepare and execute a query to check if the user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    // Fetch the user data from the database
    $user = $result->fetch_assoc();
    
    // Verify the password
    if (password_verify($password, $user['password'])) {
        // Password is correct, create a session for the user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        
        // Return session ID to the frontend instead of setting a cookie in PHP
        $sessionId = session_id();
        
        // Send success response with session ID
        echo json_encode([
            "status" => "success",
            "message" => "Sign in successful.",
            "session_id" => $sessionId, // Send session ID to the frontend
            "user" => [
                "id" => $user['id'],
                "email" => $user['email']
            ]
        ]);
    } else {
        // Invalid password
        echo json_encode([
            "status" => "error",
            "message" => "Invalid password."
        ]);
    }
} else {
    // User not found
    echo json_encode([
        "status" => "error",
        "message" => "User not found."
    ]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
