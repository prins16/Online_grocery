<?php
require_once "config/database.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Grocery Store</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- Navigation Bar -->
    <header>
        <nav class="navbar">

            <div class="logo">
                Online Grocery
            </div>

            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="products/index.php">Products</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="cart/index.php">🛒 Cart</a></li>
            </ul>

        </nav>
    </header>


    <!-- Hero Section -->
    <section class="hero">

        <div class="hero-content">

            <h1>Fresh Groceries Delivered to Your Door</h1>

            <p>
                Shop fresh fruits, vegetables, dairy products,
                snacks and everyday essentials from the comfort of your home.
            </p>

            <a href="products/index.php" class="btn">
                Shop Now
            </a>

        </div>

    </section>


    <!-- Categories Section -->
    <section class="categories">

        <h2>Shop by Category</h2>

        <div class="category-container">

            <div class="category-card">
                <h3>🍎 Fruits</h3>
                <p>Fresh and healthy fruits.</p>
            </div>

            <div class="category-card">
                <h3>🥦 Vegetables</h3>
                <p>Fresh vegetables for your family.</p>
            </div>

            <div class="category-card">
                <h3>🥛 Dairy</h3>
                <p>Milk, cheese and other dairy products.</p>
            </div>

            <div class="category-card">
                <h3>🍪 Snacks</h3>
                <p>Tasty snacks and packaged foods.</p>
            </div>

        </div>

    </section>


    <!-- Features -->
    <section class="features">

        <h2>Why Shop With Us?</h2>

        <div class="feature-container">

            <div class="feature-card">
                <h3>🚚 Fast Delivery</h3>
                <p>Get your groceries delivered quickly.</p>
            </div>

            <div class="feature-card">
                <h3>🥬 Fresh Products</h3>
                <p>Quality and fresh grocery products.</p>
            </div>

            <div class="feature-card">
                <h3>🔒 Secure Payment</h3>
                <p>Safe and secure checkout experience.</p>
            </div>

            <div class="feature-card">
                <h3>💰 Great Prices</h3>
                <p>Affordable prices and useful coupons.</p>
            </div>

        </div>

    </section>


    <!-- Footer -->
    <footer>

        <p>
            &copy; 2026 Online Grocery Store. All Rights Reserved.
        </p>

    </footer>

</body>
</html>