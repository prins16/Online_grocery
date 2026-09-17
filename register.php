<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        // Check if email already exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $message = "Email already registered.";

        } else {

            // Securely hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (fullname, email, phone, password, status)
                 VALUES (?, ?, ?, ?, 'active')"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $fullname,
                $email,
                $phone,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed. Please try again.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Online Grocery</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .register-container {
            width: 420px;
            max-width: 90%;
            margin: 60px auto;
            background: white;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #1b5e20;
        }

        .register-container label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .register-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .register-container button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 5px;
            background: #2e7d32;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .register-container button:hover {
            background: #1b5e20;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #2e7d32;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

    </style>

</head>

<body>

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


<div class="register-container">

    <h2>Create Your Account</h2>

    <?php if ($message != ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="register.php">

        <label>Full Name</label>

        <input
            type="text"
            name="fullname"
            placeholder="Enter your full name"
            required
        >


        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


        <label>Phone</label>

        <input
            type="text"
            name="phone"
            placeholder="Enter your phone number"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Create a password"
            required
        >


        <label>Confirm Password</label>

        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm your password"
            required
        >


        <button type="submit">
            Register
        </button>

    </form>


    <div class="login-link">

        Already have an account?

        <a href="login.php">
            Login here
        </a>

    </div>

</div>


<footer>

    <p>
        &copy; 2026 Online Grocery Store. All Rights Reserved.
    </p>

</footer>

</body>

</html>