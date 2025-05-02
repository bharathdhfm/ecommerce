<?php
// Start the session and check if the user is logged in
session_start();

// Logout Logic
if (isset($_POST['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: pages/login.php"); // Redirect to login page
    exit(); // Make sure no further code is executed after redirection
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: pages/login.php");
    exit();
}

// Include the database connection
include 'includes/db.php';

// Get the search query from the URL
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Fetch products that match the search query
if ($search_query) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :search_query OR description LIKE :search_query");
    $stmt->execute(['search_query' => '%' . $search_query . '%']);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = []; // No products if search query is empty
}

// Get the logged-in user's username
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Welcome, <?= htmlspecialchars($username); ?>!</h1> <!-- Display username -->
            <nav>
                <form method="GET" action="search_results.php" style="display: inline;">
                    <input type="text" name="search_query" placeholder="Search Products..." value="<?= htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="search-button">Search</button>
                </form>

                <a href="index.php">Home</a>
                <a href="pages/cart.php" class="cart-link">
                    <img src="images/cart-icon.png" alt="Cart" class="cart-icon">
                    Cart
                </a>

                <!-- Logout button -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            </nav>
        </div>
    </header>
    <div class="main-container">
        <main>
            <h2>Search Results for "<?= htmlspecialchars($search_query); ?>"</h2>
            <div class="product-list">
                <?php if (empty($products)) : ?>
                    <p>No products found matching your search query.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="product">
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <p>Price: $<?= number_format($product['price'], 2); ?></p>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            <?php if (!empty($product['image'])) : ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php endif; ?>
                            <form method="POST" action="pages/cart.php">
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
