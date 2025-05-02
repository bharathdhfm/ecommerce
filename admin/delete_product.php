<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Optional: delete image file from /images folder
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product && file_exists("../images/" . $product['image'])) {
        unlink("../images/" . $product['image']);
    }

    // Delete product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);

    header("Location: manage_products.php?msg=deleted");
    exit();
} else {
    echo "Invalid product ID.";
}
?>
