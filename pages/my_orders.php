<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id']; // User ID from session

// Fetch user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY FIELD(status, 'Pending', 'Shipped', 'Delivered', 'Cancelled'), created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            font-size: 2.2em;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 14px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 0.95em;
        }

        th {
            background-color: #f5f5f5;
        }

        .status-Pending {
            color: #d97706;
            font-weight: bold;
        }

        .status-Shipped {
            color: #3b82f6;
            font-weight: bold;
        }

        .status-Delivered {
            color: #16a34a;
            font-weight: bold;
        }

        .status-Cancelled {
            color: #dc2626;
            font-weight: bold;
        }

        .admin-message {
            font-style: italic;
            color: #555;
        }

        .back-to-shop {
            display: inline-block;
            margin-top: 30px;
            text-align: center;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-to-shop:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
                border: none;
                border-bottom: 1px solid #ddd;
            }

            td:before {
                position: absolute;
                left: 10px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                color: #666;
            }

            td:nth-of-type(1):before { content: "Order ID"; }
            td:nth-of-type(2):before { content: "Product Name"; }
            td:nth-of-type(3):before { content: "Price"; }
            td:nth-of-type(4):before { content: "Quantity"; }
            td:nth-of-type(5):before { content: "Status"; }
            td:nth-of-type(6):before { content: "Admin Message"; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Orders</h2>

        <?php if (empty($orders)): ?>
            <p style="text-align:center; font-size: 1.2em;">You have no orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Admin Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td>$<?= number_format($order['product_price'], 2) ?></td>
                            <td><?= $order['quantity'] ?></td>
                            <td class="status-<?= htmlspecialchars($order['status']) ?>"><?= $order['status'] ?></td>
                            <td class="admin-message"><?= htmlspecialchars($order['admin_message'] ?? 'No message') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div style="text-align:center;">
            <a href="../index.php" class="back-to-shop">← Back to Shop</a>
        </div>
    </div>
</body>
</html>
