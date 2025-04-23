<?php
session_start();
include 'config.php';
include 'encryption.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(["status" => "unauthorized"]);
    exit;
}

// Accept encrypted ID via AJAX POST
$id = decrypt($_POST['id'] ?? '');

if ($id) {
    // Delete the product using a prepared statement
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>
