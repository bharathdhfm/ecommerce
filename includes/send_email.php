<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

function send_email($toEmail, $toName, $orderId, $productName, $price, $status) {

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with your Gmail and app password
        $mail->Username = 'kondurubharathkumarmca@gmail.com';
        $mail->Password = 'your password'; // App Password (no spaces)


        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('kondurubharathkumarmca@gmail.com', 'Bharath Store Name');
        $mail->addAddress($toEmail, $toName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - Order #' . $orderId;
        $mail->Body = "
            <h3>Thank you for your order!</h3>
            <p>Order ID: <strong>$orderId</strong></p>
            <p>Product: $productName</p>
            <p>Price: $$price</p>
            <p>Status: $status</p>
            <br>
            <p>We appreciate your business.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
function send_order_status_email($to, $username, $order_id, $product_name, $price, $status, $admin_message) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kondurubharathkumarmca@gmail.com'; // Your Gmail address
        $mail->Password = 'eqgfrmwwalyapvvj'; // Your app password (generated from Gmail)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('kondurubharathkumarmca@gmail.com', 'Bharath Store Name');
        $mail->addAddress($to, $username); // Add recipient email and name

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Order Status Updated to: $status";
        $mail->Body = "
            Hi $username,<br><br>
            Your order (#$order_id) for <strong>$product_name</strong> (₹$price) has been updated.<br>
            <strong>Status:</strong> $status<br><br>";

        if (!empty($admin_message)) {
            $mail->Body .= "<em>Admin Message:</em><br>$admin_message<br><br>";
        }

        $mail->Body .= "Check your order history for details.<br><br>Regards,<br>Team";

        // Send email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo); // Log error if mail fails
        return false; // Failure in sending email
    }
}


?>
