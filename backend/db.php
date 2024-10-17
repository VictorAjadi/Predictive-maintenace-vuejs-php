<?php
// db.php - Make sure your database connection script looks like this:
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iotdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
