<?php
$host = 'localhost';
$port = 3307; // Specify the port
$dbname = 'ecommerce';
$user = 'root';
$password = '';

try {
    // Include the port in the connection string
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Stop further execution if connection fails
}
?>