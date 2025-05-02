<?php
include('../includes/db.php');  // Database connection
session_start();

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $mobile_number = $_POST['mobile_number']; // Get the mobile number
    $role = 'user'; // Default role for users

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<script>alert('Email is already registered!');</script>";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, mobile_number, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $mobile_number, $role]);

        // Log the user in after successful registration
        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: ../index.php"); // Redirect to the homepage
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #e74c3c;
            font-size: 1em;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2>Create Account</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Mobile Number:</label>
        <input type="tel" name="mobile_number" required pattern="[0-9]{10}" placeholder="Enter 10 digit mobile number">
        <button type="submit" name="register">Register</button>
    </form>
    <?php if (isset($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <p style="text-align:center; margin-top:10px;">Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
