<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/send_email.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$email_sent_successfully = false;

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
}

if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
}

if (isset($_POST['submit_order'])) {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $cart_item) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, product_price, quantity, status) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $product['name'], $product['price'], $quantity, 'Pending']);
        $order_id = $conn->lastInsertId();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $conn->prepare("SELECT email, username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $email_sent_successfully = send_email(
            $user['email'], 
            $user['username'], 
            $order_id,
            $product['name'], 
            $product['price'], 
            'Pending'
        );
    }

    header("Location: my_orders.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .cart-item {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .cart-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-name {
            font-size: 1.2em;
            font-weight: bold;
            color: #343a40;
        }
        .item-price {
            font-size: 1.1em;
            color: #495057;
        }
        .item-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 10px;
        }
        .item-actions form {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        .quantity {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .item-actions button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .item-actions button:hover {
            background-color: #0056b3;
        }
        .cart-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .cart-actions a,
        .cart-actions form button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1em;
            transition: background-color 0.3s;
            display: inline-block;
        }
        .cart-actions a:hover,
        .cart-actions form button:hover {
            background-color: #218838;
        }
        .total-cost {
            font-size: 1.6em;
            font-weight: bold;
            color: #343a40;
            text-align: center;
            margin-top: 20px;
        }
        .empty-cart {
            text-align: center;
            font-size: 1.2em;
            color: #6c757d;
            margin-top: 30px;
        }
        @media (min-width: 768px) {
            .cart-item {
                flex-direction: row;
                align-items: center;
            }
            .cart-actions {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Cart</h2>
        <?php if (empty($cart_items)) : ?>
            <p class='empty-cart'>Your cart is empty.</p>
        <?php else : ?>
            <?php foreach ($cart_items as $cart_item): 
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];

                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                $total_cost += $product['price'] * $quantity;
            ?>
                <div class="cart-item">
                    <div class="cart-item-row">
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="item-price">$<?= number_format($product['price'], 2) ?> × <?= $quantity ?></div>
                        </div>
                        <div class="item-actions">
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="number" name="quantity" value="<?= $quantity ?>" class="quantity" min="1" required>
                                <button type="submit" name="update_quantity">Update</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="remove_from_cart">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="total-cost">Total: $<?= number_format($total_cost, 2); ?></div>
            <div class="cart-actions">
                <a href="../index.php">Back to Shop</a>
                <a href="checkout.php">Proceed to Checkout</a>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="submit_order">Submit Order</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($email_sent_successfully): ?>
        <script>alert('Email sent successfully!');</script>
    <?php endif; ?>
</body>
</html>
