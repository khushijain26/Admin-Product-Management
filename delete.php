<?php
session_start();
include 'config.php';         // Connect to DB
include 'encryption.php';     // Decrypt encrypted ID

// Make sure the user is logged in as admin
if (!isset($_SESSION['admin'])) {
    die("Unauthorized");
}

// Decrypt the product ID from the URL
$id = decrypt($_GET['id'] ?? '');

if ($id) {
    // Delete the product by ID
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Set success message
    $_SESSION['message'] = "Product deleted successfully.";
}

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?>
