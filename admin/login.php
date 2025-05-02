<?php
include '../includes/db.php';
session_start();

require '../includes/mailer/PHPMailer.php';
require '../includes/mailer/SMTP.php';
require '../includes/mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email belongs to an admin
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Start admin session and redirect to dashboard
        $_SESSION['admin_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>Invalid credentials or not an admin.</p>";
    }
}

if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];
    
    // Check if the email belongs to an admin
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32)); // Generate reset token
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Generate the reset link
        $reset_link = "http://localhost/ecommerce/pages/reset_password.php?token=$token ";

        // Send the email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true; 
            $mail->Username = 'kondurubharathkumarmca@gmail.com';
            $mail->Password = 'eqgfrmwwalyapvvj';  // Use your Gmail app password here
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('kondurubharathkumarmca@gmail.com', 'Bharath Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Reset Your Admin Password";
            $mail->Body    = "Click the link below to reset your password:<br><a href='$reset_link'>$reset_link</a>";

            $mail->send();
            echo "<p style='color:green; text-align:center;'>Reset link has been sent to your email.</p>";
        } catch (Exception $e) {
            echo "<p style='color:red; text-align:center;'>Mailer Error: " . $mail->ErrorInfo . "</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>No admin account found with that email.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" name="login">Login</button>
        </form>

        <div class="forgot-password">
            <p><a href="#" onclick="document.getElementById('forgotPasswordForm').style.display='block';">Forgot Password?</a></p>
        </div>
    </div>

    <!-- Forgot Password Form -->
    <div id="forgotPasswordForm" style="display:none; text-align:center; max-width: 400px; margin: 20px auto;">
        <form method="POST">
            <label for="email">Enter your email</label>
            <input type="email" name="email" id="email" required>
            <button type="submit" name="forgot_password">Send Reset Link</button>
        </form>
    </div>

</body>
</html>
