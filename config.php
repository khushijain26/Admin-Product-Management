<?php
// Database connection credentials
$host = 'localhost';   // Usually localhost
$db   = 'product_db';  // Your database name
$user = 'root';        // MySQL username (MAMP: root)
$pass = 'root';        // MySQL password (MAMP: root)

// Create MySQL connection using mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Check if connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
