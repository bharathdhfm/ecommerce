<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$category = $_GET['type'] ?? '';
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
$stmt->execute([$category]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$username = $_SESSION['username'] ?? 'Guest';
$first_name = strtok($username, " ");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category); ?> - All Products</title>
    <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>
<header>
    <div class="header-container">
        <h1><?= htmlspecialchars($category); ?> Products - Welcome, <?= htmlspecialchars($first_name); ?>!</h1>
        <nav>
            <a href="my_orders.php">MY Orders</a>
            <a href="cart.php" class="cart-link">
                <img src="../images/cart-icon.png" alt="Cart" class="cart-icon"> Cart
            </a>
            <a href="../index.php">Home</a>
        </nav>
    </div>
</header>

<div class="main-container">
    <main>
        <div class="product-list">
            <?php if (empty($products)): ?>
                <p>No products found in <?= htmlspecialchars($category); ?>.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <h3><?= htmlspecialchars($product['name']); ?></h3>
                        <p>Price: $<?= number_format($product['price'], 2); ?></p>
                        <p><?= htmlspecialchars($product['description']); ?></p>
                        <?php if (!empty($product['image'])): ?>
    <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
<?php endif; ?>

                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<footer>
    <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
</footer>
</body>
</html>
