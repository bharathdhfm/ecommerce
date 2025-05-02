<?php
include('../includes/db.php');

$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?");
    $stmt->execute([$new_password, $token]);

    if ($stmt->rowCount()) {
        $message = "✅ Password updated successfully.";
    } else {
        $message = "❌ Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
        background: #e8f0fe;
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .container {
        background: white;
        padding: 30px 25px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        width: 90%;
        max-width: 400px;
        text-align: center;
    }
    h2 {
        margin-bottom: 20px;
        color: #333;
    }
    input[type="password"], button {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
    }
    button {
        background: #28a745;
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: #218838;
    }
    a {
        text-decoration: none;
        color: #007bff;
    }
    a:hover {
        text-decoration: underline;
    }
    p {
        margin-top: 12px;
        color: #444;
    }
    @media (max-width: 480px) {
        .container {
            padding: 20px;
        }
        input, button {
            font-size: 15px;
        }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Reset Password</h2>
    <form method="POST">
      <input type="password" name="password" required placeholder="Enter new password">
      <button type="submit">Reset</button>
      <p><?= htmlspecialchars($message); ?></p>
      <p>Return to <a href="login.php">Login</a></p>
    </form>
  </div>
</body>
</html>
