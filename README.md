ECOMMERCE/
│
├── admin/                # Admin panel files (add, edit, delete products & view orders)
│   ├── add_admin.php
│   ├── add_product.php
│   ├── admin_orders.php
│   ├── dashboard.php
│   ├── delete_product.php
│   ├── edit_product.php
│   ├── login.php
│   ├── logout.php
│   └── manage_products.php
│
├── CSS/
│   └── style.css         # Main site styles
│
├── images/               # Product and UI images
│
├── includes/
│   └── db.php            # Database connection
│
├── mailer/               # PHPMailer for order email notifications
│   ├── PHPMailer.php
│   ├── SMTP.php
│   ├── Exception.php
│   └── send_email.php
│
├── cart.php
├── category.php
├── checkout.php
├── forgot_password.php
├── login.php
├── logout.php
├── my_orders.php
├── register.php
├── reset_password.php
├── index.php
├── search_results.php
├── test_db.php
├── .gitignore
└── README.md
Features 
🚀 Features
User registration & login (with sessions)

Product browsing and search

Add to cart and checkout

My orders page for customers

Admin dashboard to manage products and orders

Email notifications using PHPMailer

Password reset via email

Secure sessions and input sanitization

🛠 Setup Instructions
Clone the Repository

Copy
git clone https://github.com/your-username/php-ecommerce.git
cd php-ecommerce

Database Setup
CREATE DATABASE ecommerce;
USE ecommerce;
-- Run your tables and sample data
Configure Database Connection
$host = 'localhost';
$db   = 'ecommerce';
$user = 'root';
$pass = ''; // Your DB password

PHPMailer Configuration

Update mailer/send_email.php with your SMTP settings.

Run the Application

Use XAMPP, MAMP, or any PHP server.

Visit: http://localhost/php-ecommerce/index.php
🧭 Workflow
🛒 User Side
Register/Login

Users can register or log in to their account.

Browse & Search Products

Products displayed by category on homepage and searchable via a search bar.

Add to Cart & Checkout

Users can add products to cart and proceed to checkout.

Place Order

On checkout, user fills details and places the order.

✅ An email is sent to the admin with the order details.

View Order History

Users can see their submitted orders on the My Orders page.

🛠 Admin Side
Admin Login

Admin logs in via admin/login.php.

Dashboard

View summary and links to manage products and orders.

Manage Products

Add, edit, delete products from the catalog.

Manage Orders

View all orders placed by users.

Update order status (e.g., "Processing", "Shipped", "Completed").

✅ Email Notification on Status Update

When the admin updates an order status,
📧 an email is automatically sent to the customer notifying them of the update.

📧 Email Notifications
✔️ On Order Placement – Email sent to admin
✔️ On Status Update by Admin – Email sent to customer

SMTP settings can be updated in mailer/send_email.php.

If you have any doubts contact me bharathkonduri5@gmail.com


















