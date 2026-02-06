<?php
session_start();
include 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if product belongs to user
$query = "SELECT image_url FROM products WHERE id = ? AND seller_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my_products.php");
    exit();
}

$product = $result->fetch_assoc();

// Delete image file
if (file_exists($product['image_url'])) {
    unlink($product['image_url']);
}

// Delete product from database
$delete_query = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();

header("Location: my_products.php?success=deleted");
exit();
?>