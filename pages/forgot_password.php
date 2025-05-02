<?php
include('../includes/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../includes/mailer/PHPMailer.php';
require '../includes/mailer/SMTP.php';
require '../includes/mailer/Exception.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$token, $email]);

        $reset_link = "http://localhost/ecommerce/pages/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kondurubharathkumarmca@gmail.com';
            $mail->Password = 'eqgfrmwwalyapvvj';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('kondurubharathkumarmca@gmail.com', 'Shop Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Reset Your Password";
            $mail->Body = "Click the link below to reset your password:<br><a href='$reset_link'>$reset_link</a>";

            $mail->send();
            $message = "✅ Reset link sent to your email.";
        } catch (Exception $e) {
            $message = "❌ Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $message = "❌ No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
        background: #f5f7fa;
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
    input[type="email"], button {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
    }
    button {
        background: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: #0056b3;
    }
    p {
        color: #444;
        margin-top: 10px;
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
    <h2>Forgot Password</h2>
    <form method="POST">
      <input type="email" name="email" required placeholder="Enter your email">
      <button type="submit">Send Reset Link</button>
      <p><?= htmlspecialchars($message); ?></p>
    </form>
  </div>
</body>
</html>
