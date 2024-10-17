<?php

// Set CORS headers to allow requests from your frontend (localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Access-Control-Allow-Credentials: true'); // Allows credentials to be sent
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
include '../db.php';

// Start session
session_start();

// Check if user is authenticated
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $data = json_decode(file_get_contents("php://input"), true);
    $weight = $data['weight'];

    // Prepare a statement to insert the new weight
    $stmt = $conn->prepare("INSERT INTO weights (weight, user_id) VALUES (?, ?)");
    $stmt->bind_param("di", $weight, $user_id); // d for double, i for integer

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Weight added successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $stmt->error
        ]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "User not authenticated."
    ]);
}

// Close the database connection
$conn->close();
?>
