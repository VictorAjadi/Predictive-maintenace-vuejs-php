<?php
// Set CORS headers to allow requests from your frontend (localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // This is crucial for allowing credentials
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
include '../db.php'; // Make sure this is the correct path to your db.php

// Start the session
session_start();

// Get the input data from the request body (username, email, password)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input: " . json_last_error_msg()]);
    exit();
}

$username = $data['username'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$username || !$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit();
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password

// Prepare and execute an insert query to save the new user
if ($conn) { // Ensure the connection is still open
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

    if ($stmt) { // Check if the statement preparation was successful
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            // Upon successful sign-up, create a session and store user information
            $user_id = $stmt->insert_id;  // Get the ID of the newly inserted user

            // Store the user info in the session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;

            // Set the session cookie
            setcookie("session", session_id(), time() + 3600, "/", "", false, true); // Secure and HttpOnly flags

            // Send success response along with the user info
            echo json_encode([
                "status" => "success",
                "message" => "User registered successfully.",
                "user" => [
                    "id" => $user_id,
                    "username" => $username,
                    "email" => $email
                ]
            ]);
        } else {
            // In case of an execution error
            echo json_encode(["status" => "error", "message" => "Execution failed: " . $stmt->error]);
        }
        // Close the statement
        $stmt->close();
    } else {
        // In case of statement preparation error
        echo json_encode(["status" => "error", "message" => "Statement preparation failed: " . $conn->error]);
    }
} else {
    // Connection failed
    echo json_encode(["status" => "error", "message" => "Connection to database failed."]);
}

// Close the database connection
$conn->close();
?>
