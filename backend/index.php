<?php
// Disable CORS by allowing all origins and methods
header("Access-Control-Allow-Origin: *"); // Allow any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow any HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow custom headers
header('Content-Type: application/json');

// Handle preflight OPTIONS request globally
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(); // No need to proceed further for OPTIONS request
}

// Include the database connection file
include './db.php';

// Start a session
session_start();

// Get the request method (GET, POST, etc.) and the requested URL path
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Remove any query string from the URI
$uri = strtok($requestUri, '?');

// Routing logic
switch ($uri) {
    case '/api/add-user':
        if ($requestMethod === 'POST') {
            include './api/adduser.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;
    
    case '/api/add-weight':
        if ($requestMethod === 'POST') {
            include './api/addweight.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;
    
    case '/api/get-weights':
        if ($requestMethod === 'GET') {
            include './api/getweights.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;
    
    case '/api/sign-in':
        if ($requestMethod === 'POST') {
            include './api/signin.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;
    
    case '/api/user-details':
        if ($requestMethod === 'GET') {
            include './api/userdetails.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;
    
    case '/api/logout':
        if ($requestMethod === 'POST') {
            include './api/logout.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(["message" => "Method not allowed"]);
        }
        break;

    default:
        http_response_code(404); // Not Found
        echo json_encode(["message" => "Endpoint not found"]);
        break;
}

// Close the database connection
$conn->close();
?>
