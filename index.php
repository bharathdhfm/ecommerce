<?php
session_start();

// Inactivity timeout
$timeout_duration = 600;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: pages/login.php");
    exit();
}
$_SESSION['last_activity'] = time();

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: pages/login.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}

include 'includes/db.php';

function getProductsByCategory($conn, $category, $limit = 6) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? LIMIT ?");
    $stmt->bindValue(1, $category);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = ['Mobiles', 'Electronics', 'Shoes', 'Clothes'];

$username = $_SESSION['username'] ?? 'Guest';
$first_name = strtok($username, " ");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .slider-container { width: 100%; overflow: hidden; position: relative; height: 400px; margin: 20px 0; }
        .slider-wrapper { display: flex; transition: transform 0.8s ease-in-out; width: 300%; }
        .slider-img { width: 100%; height: 400px; object-fit: cover; flex-shrink: 0; }
        @media (max-width: 768px) {
            .slider-container, .slider-img { height: 200px; }
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <h1>Welcome, <?= htmlspecialchars($first_name); ?>!</h1>
        <nav>
            <form method="GET" action="search_results.php" style="display: inline;">
                <input type="text" name="search_query" placeholder="Search Products..." required class="search-input">
                <button type="submit" class="search-button">Search</button>
            </form>
            <a href="pages/my_orders.php">MY Orders</a>
            <a href="pages/cart.php" class="cart-link">
                <img src="images/cart-icon.png" alt="Cart" class="cart-icon"> Cart
            </a>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-button">Logout</button>
            </form>
        </nav>
    </div>
</header>

<div class="slider-container">
    <div class="slider-wrapper" id="sliderWrapper">
        <img src="images/banner1.jpg" alt="Banner 1" class="slider-img">
        <img src="images/banner2.jpg" alt="Banner 2" class="slider-img">
        <img src="images/banner3.png" alt="Banner 3" class="slider-img">
    </div>
</div>

<div class="main-container">
    <main>
        <?php foreach ($categories as $category): ?>
            <h2><?= htmlspecialchars($category); ?></h2>
            <div class="product-list">
                <?php
                $products = getProductsByCategory($conn, $category);
                if (empty($products)) {
                    echo "<p>No products available in $category.</p>";
                } else {
                    foreach ($products as $product): ?>
                        <div class="product">
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <p>Price: $<?= number_format($product['price'], 2); ?></p>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            <?php if (!empty($product['image'])): ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php endif; ?>
                            <form method="POST" action="pages/cart.php">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach;
                } ?>
            </div>
            <div style="text-align: right; margin-bottom: 40px;">
                <a href="pages/category.php?type=<?= urlencode($category); ?>">Show All</a>
            </div>
        <?php endforeach; ?>
    </main>
</div>

<footer>
    <p>&copy; <?= date('Y'); ?> Online Store. All rights reserved.</p>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentIndex = 0;
        const sliderWrapper = document.getElementById('sliderWrapper');
        const totalSlides = sliderWrapper.children.length;
        setInterval(() => {
            currentIndex = (currentIndex + 1) % totalSlides;
            sliderWrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
        }, 3000);
    });
</script>
</body>
</html>
