
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Us - Online Grocery</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f7faf7;
            color: #333;
        }


        /* ================= NAVBAR ================= */

        .navbar {
            background: #2e7d32;
            padding: 18px 6%;

            display: flex;
            align-items: center;
            justify-content: space-between;

            box-shadow: 0 2px 10px rgba(0,0,0,0.10);
        }

        .logo {
            color: white;
            font-size: 27px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: #c8e6c9;
        }

        .cart-link {
            background: white;
            color: #2e7d32 !important;
            padding: 9px 15px;
            border-radius: 7px;
            font-weight: bold;
        }


        /* ================= HERO ================= */

        .about-hero {
            text-align: center;
            padding: 75px 20px 55px;

            background:
                linear-gradient(
                    135deg,
                    #e8f5e9,
                    #ffffff
                );
        }

        .about-label {
            display: inline-block;

            background: #dcedc8;
            color: #2e7d32;

            padding: 8px 18px;
            border-radius: 30px;

            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1.5px;

            margin-bottom: 18px;
        }

        .about-hero h1 {
            color: #1b5e20;
            font-size: 46px;
            margin-bottom: 18px;
        }

        .about-hero h1 span {
            color: #f39c12;
        }

        .about-hero p {
            max-width: 700px;
            margin: auto;

            color: #666;
            font-size: 17px;
            line-height: 1.8;
        }


        /* ================= FEATURES ================= */

        .features-section {
            max-width: 1100px;
            margin: 55px auto;
            padding: 0 25px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .section-title h2 {
            color: #1b5e20;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .section-title p {
            color: #777;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .feature-card {
            background: white;

            padding: 32px 25px;

            text-align: center;

            border-radius: 14px;

            box-shadow:
                0 5px 18px rgba(0,0,0,0.07);

            border: 1px solid #edf3ed;

            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-6px);

            box-shadow:
                0 10px 25px rgba(0,0,0,0.10);
        }

        .feature-icon {
            width: 65px;
            height: 65px;

            margin: 0 auto 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #e8f5e9;

            font-size: 30px;
        }

        .feature-card h3 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .feature-card p {
            color: #777;
            line-height: 1.6;
            font-size: 15px;
        }


        /* ================= MISSION ================= */

        .mission {
            max-width: 1000px;
            margin: 60px auto;

            padding: 40px 30px;

            background: #2e7d32;

            border-radius: 16px;

            text-align: center;

            color: white;

            box-shadow:
                0 8px 25px rgba(46,125,50,0.18);
        }

        .mission h2 {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .mission p {
            max-width: 750px;
            margin: auto;

            color: #e8f5e9;

            line-height: 1.7;
        }


        /* ================= SIMPLE STATS ================= */

        .stats {
            max-width: 1000px;
            margin: 50px auto;

            padding: 0 25px;

            display: grid;
            grid-template-columns: repeat(3, 1fr);

            gap: 20px;
        }

        .stat {
            text-align: center;
            padding: 20px;
        }

        .stat h2 {
            color: #2e7d32;
            font-size: 30px;
            margin-bottom: 5px;
        }

        .stat p {
            color: #777;
        }


        /* ================= FOOTER ================= */

        footer {
            background: #1b5e20;
            color: white;

            padding: 35px 6% 20px;

            margin-top: 60px;
        }

        .footer-content {
            max-width: 1100px;
            margin: auto;

            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
        }

        .footer-content h3 {
            margin-bottom: 14px;
        }

        .footer-content p {
            color: #dcedc8;
            line-height: 1.6;
        }

        .footer-links a {
            display: block;

            color: #dcedc8;

            text-decoration: none;

            margin-bottom: 9px;
        }

        .footer-links a:hover {
            color: white;
        }

        .copyright {
            max-width: 1100px;
            margin: 25px auto 0;

            padding-top: 18px;

            border-top: 1px solid rgba(255,255,255,0.2);

            text-align: center;

            color: #c8e6c9;

            font-size: 14px;
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 800px) {

            .navbar {
                flex-direction: column;
                gap: 18px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .about-hero h1 {
                font-size: 36px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

        }

    </style>

</head>


<body>


<!-- ================= NAVBAR ================= -->

<header class="navbar">

    <div class="logo">
        🛒 Online Grocery
    </div>

    <nav class="nav-links">

        <a href="index.php">
            Home
        </a>

        <a href="products/index.php">
            Products
        </a>

        <a href="about.php">
            About Us
        </a>

        <a href="contact.php">
            Contact
        </a>

        <a href="login.php">
            Login
        </a>

        <a href="register.php">
            Register
        </a>

        <a href="cart/index.php" class="cart-link">
            🛒 Cart
        </a>

    </nav>

</header>


<!-- ================= ABOUT HERO ================= -->

<section class="about-hero">

    <div class="about-label">
        ABOUT US
    </div>

    <h1>
    Everything You Need,
    <span>All in One Place.</span>
</h1>

<p>
    Shop fresh groceries, everyday essentials and your
    favourite products — all from one convenient place.
    Discover, choose, and order your groceries with just
    a few clicks.
</p>
</section>


<!-- ================= FEATURES ================= -->

<section class="features-section">

    <div class="section-title">

        <h2>
            Why Choose Us?
        </h2>

        <p>
            Everything you need for a simple grocery experience.
        </p>

    </div>


    <div class="feature-grid">


        <!-- CARD 1 -->

        <div class="feature-card">

            <div class="feature-icon">
                🥬
            </div>

            <h3>
                Quality Products
            </h3>

            <p>
                We provide a variety of grocery products
                for your everyday needs.
            </p>

        </div>


        <!-- CARD 2 -->

        <div class="feature-card">

            <div class="feature-icon">
                🛒
            </div>

            <h3>
                Easy Shopping
            </h3>

            <p>
                Browse products, add them to your cart
                and place your order easily.
            </p>

        </div>


        <!-- CARD 3 -->

        <div class="feature-card">

            <div class="feature-icon">
                🚚
            </div>

            <h3>
                Convenient Delivery
            </h3>

            <p>
                Enjoy a convenient shopping experience
                from your home.
            </p>

        </div>


    </div>

</section>


<!-- ================= STATS ================= -->

<section class="stats">

    <div class="stat">

        <h2>
            100+
        </h2>

        <p>
            Grocery Products
        </p>

    </div>


    <div class="stat">

        <h2>
            24/7
        </h2>

        <p>
            Online Shopping
        </p>

    </div>


    <div class="stat">

        <h2>
            100%
        </h2>

        <p>
            Customer Focus
        </p>

    </div>

</section>


<!-- ================= MISSION ================= -->

<section class="mission">

    <h2>
        Our Mission
    </h2>

    <p>
        Our mission is to make grocery shopping
        simple, fast and convenient for everyone.
        We want to give customers an easy way
        to discover products and shop for their
        everyday needs online.
    </p>

</section>


<!-- ================= FOOTER ================= -->

<footer>

    <div class="footer-content">


        <div>

            <h3>
                🛒 Online Grocery
            </h3>

            <p>
                Your simple and convenient
                online grocery store for
                everyday essentials.
            </p>

        </div>


        <div class="footer-links">

            <h3>
                Quick Links
            </h3>

            <a href="index.php">
                Home
            </a>

            <a href="products/index.php">
                Products
            </a>

            <a href="about.php">
                About Us
            </a>

            <a href="contact.php">
                Contact
            </a>

        </div>


        <div class="footer-links">

            <h3>
                Account
            </h3>

            <a href="login.php">
                Login
            </a>

            <a href="register.php">
                Register
            </a>

            <a href="cart/index.php">
                🛒 Cart
            </a>

        </div>


    </div>


    <div class="copyright">

        © 2026 Online Grocery Store.
        All Rights Reserved.

    </div>

</footer>


</body>

</html>
