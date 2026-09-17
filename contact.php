
<?php

session_start();

require_once "config/database.php";

$message_sent = false;
$error_message = "";


/* ==========================================
   SAVE CONTACT MESSAGE
========================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");


    if (
        $name === "" ||
        $email === "" ||
        $subject === "" ||
        $message === ""
    ) {

        $error_message = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error_message = "Please enter a valid email address.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO contact_messages
            (name, email, subject, message)
            VALUES (?, ?, ?, ?)"
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $name,
                $email,
                $subject,
                $message
            );


           if (mysqli_stmt_execute($stmt)) {

    header("Location: message-success.php");
    exit();

}else {

                $error_message =
                    "Unable to send your message. Please try again.";

            }


            mysqli_stmt_close($stmt);

        } else {

            $error_message =
                "Database error. Please try again.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Contact Us - Online Grocery
    </title>


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

            box-shadow:
                0 2px 10px
                rgba(0,0,0,0.10);

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

        .contact-hero {

            text-align: center;

            padding: 65px 20px 45px;

            background:
                linear-gradient(
                    135deg,
                    #e8f5e9,
                    #ffffff
                );

        }


        .contact-label {

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


        .contact-hero h1 {

            color: #1b5e20;

            font-size: 44px;

            margin-bottom: 15px;

        }


        .contact-hero h1 span {

            color: #f39c12;

        }


        .contact-hero p {

            max-width: 650px;

            margin: auto;

            color: #666;

            font-size: 17px;

            line-height: 1.7;

        }


        /* ================= ALERT ================= */

        .alert {

            max-width: 1050px;

            margin: 25px auto 0;

            padding: 15px 20px;

            border-radius: 8px;

            text-align: center;

            font-weight: bold;

        }


        .success {

            background: #e8f5e9;

            color: #2e7d32;

            border: 1px solid #a5d6a7;

        }


        .error {

            background: #ffebee;

            color: #c62828;

            border: 1px solid #ef9a9a;

        }


        /* ================= CONTACT AREA ================= */

        .contact-section {

            max-width: 1050px;

            margin: 50px auto;

            padding: 0 25px;

            display: grid;

            grid-template-columns:
                0.8fr 1.2fr;

            gap: 30px;

        }


        /* ================= CONTACT INFO ================= */

        .contact-info {

            background: #2e7d32;

            color: white;

            padding: 35px;

            border-radius: 16px;

            box-shadow:
                0 8px 25px
                rgba(46,125,50,0.18);

        }


        .contact-info h2 {

            font-size: 28px;

            margin-bottom: 15px;

        }


        .contact-info > p {

            color: #e8f5e9;

            line-height: 1.7;

            margin-bottom: 28px;

        }


        .info-item {

            display: flex;

            align-items: flex-start;

            gap: 15px;

            margin-bottom: 22px;

        }


        .info-icon {

            width: 42px;

            height: 42px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            background: white;

            color: #2e7d32;

            border-radius: 50%;

            font-size: 19px;

        }


        .info-item h3 {

            font-size: 16px;

            margin-bottom: 4px;

        }


        .info-item p {

            color: #dcedc8;

            font-size: 14px;

        }


        /* ================= FORM ================= */

        .contact-form {

            background: white;

            padding: 35px;

            border-radius: 16px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.07);

        }


        .contact-form h2 {

            color: #1b5e20;

            font-size: 28px;

            margin-bottom: 8px;

        }


        .form-subtitle {

            color: #777;

            font-size: 14px;

            margin-bottom: 25px;

        }


        .form-row {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 15px;

        }


        .form-group {

            margin-bottom: 18px;

        }


        .form-group label {

            display: block;

            color: #444;

            font-size: 14px;

            font-weight: bold;

            margin-bottom: 7px;

        }


        .form-group input,
        .form-group textarea {

            width: 100%;

            padding: 12px 14px;

            border: 1px solid #ddd;

            border-radius: 8px;

            font-family: Arial, sans-serif;

            font-size: 14px;

            outline: none;

            transition: 0.2s;

        }


        .form-group input:focus,
        .form-group textarea:focus {

            border-color: #2e7d32;

            box-shadow:
                0 0 0 3px
                rgba(46,125,50,0.08);

        }


        .form-group textarea {

            min-height: 130px;

            resize: vertical;

        }


        .submit-btn {

            width: 100%;

            border: none;

            background: #2e7d32;

            color: white;

            padding: 13px;

            border-radius: 8px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;

        }


        .submit-btn:hover {

            background: #1b5e20;

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

            grid-template-columns:
                2fr 1fr 1fr;

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

            border-top:
                1px solid
                rgba(255,255,255,0.2);

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


            .contact-section {

                grid-template-columns: 1fr;

            }


            .form-row {

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

        <a
            href="cart/index.php"
            class="cart-link"
        >
            🛒 Cart
        </a>

    </nav>

</header>


<!-- ================= HERO ================= -->

<section class="contact-hero">

    <div class="contact-label">
        CONTACT US
    </div>


    <h1>

        We'd Love to
        <span>Hear From You.</span>

    </h1>


    <p>

        Have a question, suggestion or need help
        with your order? Send us a message and
        we'll be happy to help.

    </p>

</section>


<?php if ($message_sent): ?>

    <div class="alert success">

        ✅ Your message has been sent successfully!

    </div>

<?php endif; ?>


<?php if ($error_message !== ""): ?>

    <div class="alert error">

        ❌
        <?php
        echo htmlspecialchars($error_message);
        ?>

    </div>

<?php endif; ?>


<!-- ================= CONTACT SECTION ================= -->

<section class="contact-section">


    <!-- CONTACT INFORMATION -->

    <div class="contact-info">

        <h2>
            Get In Touch
        </h2>


        <p>

            We're here to help.
            Contact us using any of
            the details below.

        </p>


        <div class="info-item">

            <div class="info-icon">
                📧
            </div>

            <div>

                <h3>
                    Email
                </h3>

                <p>
                    support@onlinegrocery.com
                </p>

            </div>

        </div>


        <div class="info-item">

            <div class="info-icon">
                📞
            </div>

            <div>

                <h3>
                    Phone
                </h3>

                <p>
                    +91 98765 43210
                </p>

            </div>

        </div>


        <div class="info-item">

            <div class="info-icon">
                📍
            </div>

            <div>

                <h3>
                    Location
                </h3>

                <p>
                    Gujarat, India
                </p>

            </div>

        </div>


        <div class="info-item">

            <div class="info-icon">
                🕒
            </div>

            <div>

                <h3>
                    Support Hours
                </h3>

                <p>
                    Monday - Saturday
                    <br>
                    9:00 AM - 6:00 PM
                </p>

            </div>

        </div>

    </div>


    <!-- CONTACT FORM -->

    <div class="contact-form">

        <h2>
            Send Us a Message
        </h2>


        <p class="form-subtitle">

            Fill in the form and we'll
            get back to you.

        </p>


        <form
            action="contact.php"
            method="POST"
        >


            <div class="form-row">


                <div class="form-group">

                    <label>
                        Your Name
                    </label>


                    <input
                        type="text"
                        name="name"
                        placeholder="Enter your name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Email Address
                    </label>


                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


            </div>


            <div class="form-group">

                <label>
                    Subject
                </label>


                <input
                    type="text"
                    name="subject"
                    placeholder="What is your message about?"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Message
                </label>


                <textarea
                    name="message"
                    placeholder="Write your message here..."
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="submit-btn"
            >

                Send Message

            </button>


        </form>

    </div>


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
