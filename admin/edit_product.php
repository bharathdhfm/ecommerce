<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

// Check if an ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from the database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no product is found
    if (!$product) {
        echo "Product not found.";
        exit();
    }
} else {
    echo "Invalid product ID.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Check if an image is uploaded
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $old_image = $product['image'];
        $image_name = basename($_FILES['image']['name']);
        $image_path = "../images/" . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            if ($old_image && file_exists("../images/" . $old_image)) {
                unlink("../images/" . $old_image);
            }

            // Update with image
            $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $category, $price, $description, $image_name, $product_id]);
        }
    } else {
        // Update without image
        $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $category, $price, $description, $product_id]);
    }

    header("Location: manage_products.php?msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="number"], textarea, select {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        input[type="file"] {
            padding: 10px;
        }
        .btn-submit {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="Mobiles" <?= $product['category'] == 'Mobiles' ? 'selected' : ''; ?>>Mobiles</option>
            <option value="Electronics" <?= $product['category'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
            <option value="Shoes" <?= $product['category'] == 'Shoes' ? 'selected' : ''; ?>>Shoes</option>
            <option value="Clothes" <?= $product['category'] == 'Clothes' ? 'selected' : ''; ?>>Clothes</option>
        </select>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

        <label for="image">Product Image</label>
        <input type="file" id="image" name="image">

        <button type="submit" class="btn-submit">Update Product</button>
    </form>
    <a href="manage_products.php">Back to Manage Products</a>
</div>

</body>
</html>
