<?php

session_start();

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, fullname, email, password, status
         FROM users
         WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if ($user["status"] !== "active") {

            $message = "Your account is blocked.";

        } elseif (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fullname"] = $user["fullname"];
            $_SESSION["email"] = $user["email"];

            header("Location: user/profile.php");
            exit();

        } else {

            $message = "Invalid email or password.";
        }

    } else {

        $message = "Invalid email or password.";
    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Online Grocery</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        .login-container {
            width: 400px;
            max-width: 90%;
            margin: 70px auto;
            background: white;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        .login-container h2 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 25px;
        }

        .login-container label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
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

        .login-container button:hover {
            background: #1b5e20;
        }

        .message {
            text-align: center;
            color: #d32f2f;
            margin-bottom: 15px;
        }

        .register-link {
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


<div class="login-container">

    <h2>Login to Your Account</h2>

    <?php if ($message != ""): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="login.php">

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >


        <button type="submit">
            Login
        </button>

    </form>


    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Register here
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