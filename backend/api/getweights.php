<?php
// Set CORS headers to allow requests from your frontend (localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Access-Control-Allow-Credentials: true'); // Ensure credentials are allowed
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

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if the connection is still open
    if ($conn->ping()) {
        // Prepare a statement to retrieve weights for the logged-in user
        $stmt = $conn->prepare("SELECT * FROM weights WHERE user_id = ? ORDER BY created_at DESC");

        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all weights
            $weights = [];
            while ($row = $result->fetch_assoc()) {
                $weights[] = $row;
            }

            // Return the weights as a JSON response
            echo json_encode([
                "status" => "success",
                "weights" => $weights
            ]);

            // Close the statement
            $stmt->close();
        } else {
            // In case of statement preparation error
            echo json_encode(["status" => "error", "message" => "Statement preparation failed: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Connection is closed or failed."]);
    }
} else {
    // User not logged in
    echo json_encode([
        "status" => "error",
        "message" => "User not authenticated."
    ]);
}

// Close the database connection
if ($conn->ping()) {
    $conn->close();
}
?>
