<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.quantity, p.name, p.price, p.description, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize total
$total = 0;

// Calculate total price
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get customer details from POST request (if form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $mandal = $_POST['mandal'];
    $state = $_POST['state'];

    // Prepare WhatsApp message
    $whatsapp_number = "917780279027"; // Replace with your number
    $message = "🛒 *New Order Details*\n\n";
    $message .= "*Customer Name:* {$name}\n";
    $message .= "*Address:* {$address}\n";
    $message .= "*District:* {$district}\n";
    $message .= "*Mandal:* {$mandal}\n";
    $message .= "*State:* {$state}\n\n";
    
    // Add cart items to the message
    foreach ($items as $item) {
        $message .= "*Product:* {$item['name']}\n";
        $message .= "*Price:* \${$item['price']}\n";
        $message .= "*Qty:* {$item['quantity']}\n";
        $message .= "*Desc:* {$item['description']}\n";
    }

    $message .= "*Total Amount:* \${$total}\n";
    
    // Encode the message for URL
    $encoded_message = urlencode($message);
    $wa_url = "https://wa.me/{$whatsapp_number}?text={$encoded_message}";
    
    // Redirect to WhatsApp
    header("Location: $wa_url");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f9f9f9;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        .item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .item img {
            max-width: 100px;
            border-radius: 5px;
            margin-right: 15px;
        }
        .item p {
            margin: 5px 0;
            flex: 1;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background-color: #25D366;
            color: white;
            text-align: center;
            padding: 15px;
            border: none;
            font-size: 1.1em;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .form-group input[type="submit"]:hover {
            background-color: #1ebe5b;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            .item {
                flex-direction: column;
                align-items: flex-start;
            }
            .item img {
                max-width: 80px;
                margin-bottom: 10px;
            }
            .total {
                font-size: 1em;
                text-align: left;
            }
        }

        @media (max-width: 480px) {
            .total {
                font-size: 1.1em;
                text-align: left;
            }
            .form-group input[type="submit"] {
                padding: 12px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Preview</h2>
        <form action="checkout.php" method="POST">
            <!-- Customer Details -->
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <input type="text" id="district" name="district" required>
            </div>
            <div class="form-group">
                <label for="mandal">Mandal</label>
                <input type="text" id="mandal" name="mandal" required>
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required>
            </div>
            <!-- Product Items -->
            <?php foreach ($items as $item): ?>
                <div class="item">
                    <img src="../images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div>
                        <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                        <p>Price: $<?= $item['price'] ?></p>
                        <p>Qty: <?= $item['quantity'] ?></p>
                        <p>Description: <?= htmlspecialchars($item['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <p class="total">Total: $<?= number_format($total, 2) ?></p>
            <div class="form-group">
                <input type="submit" value="Send Order via WhatsApp">
            </div>
        </form>
    </div>
</body>
</html>
