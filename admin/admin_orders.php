<?php
session_start();
include '../includes/db.php';
include '../includes/send_email.php'; // Include at the top

// Fetch all orders with usernames, emails, and mobile numbers
$stmt = $conn->prepare("
    SELECT orders.*, users.username, users.email, users.mobile_number 
    FROM orders 
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $message = $_POST['admin_message'];

    // ✅ Update order status
    $updateStmt = $conn->prepare("UPDATE orders SET status = ?, admin_message = ? WHERE id = ?");
    if ($updateStmt->execute([$new_status, $message, $order_id])) {

        // ✅ Fetch updated order details
        $fetchStmt = $conn->prepare("
            SELECT orders.*, users.username, users.email 
            FROM orders 
            JOIN users ON orders.user_id = users.id 
            WHERE orders.id = ?
        ");
        $fetchStmt->execute([$order_id]);
        $updated_order = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Send email notification
        if ($updated_order) {
            send_order_status_email(
                $updated_order['email'],
                $updated_order['username'],
                $updated_order['id'],
                $updated_order['product_name'],
                $updated_order['product_price'],
                $new_status,
                $message
            );
        }
    }

    // ✅ Redirect after update
    header("Location: admin_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - All Orders</title>
</head>
<body>
    <h2>All Orders</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Username</th>
            <th>User Email</th>
            <th>User Mobile</th>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Status</th>
            <th>Admin Message</th>
            <th>Update</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <form method="POST">
                <td><?= $order['id'] ?></td>
                <td><?= $order['user_id'] ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['mobile_number']) ?></td>
                <td><?= htmlspecialchars($order['product_name']) ?></td>
                <td>₹<?= number_format($order['product_price'], 2) ?></td>
                <td><?= $order['quantity'] ?></td>
                <td>
                    <select name="status">
                        <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </td>
                <td>
                    <textarea name="admin_message" rows="2" cols="25" placeholder="Optional message"><?= htmlspecialchars($order['admin_message'] ?? '') ?></textarea>
                </td>
                <td>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <button type="submit" name="update_status">Update</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
