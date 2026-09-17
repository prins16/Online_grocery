<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - Online Grocery</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        .profile-container {
            width: 500px;
            max-width: 90%;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .profile-container h2 {
            color: #1b5e20;
            margin-bottom: 25px;
        }

        .profile-info {
            text-align: left;
            margin: 20px 0;
        }

        .profile-info p {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .logout-btn {
            display: inline-block;
            background: #c62828;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background: #8e0000;
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

            <li><a href="../index.php">Home</a></li>
            <li><a href="../products/index.php">Products</a></li>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="../cart/index.php">🛒 Cart</a></li>

        </ul>

    </nav>

</header>


<div class="profile-container">

    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["fullname"]); ?>! 👋</h2>

    <div class="profile-info">

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($_SESSION["fullname"]); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($_SESSION["email"]); ?>
        </p>

    </div>

    <a href="../logout.php" class="logout-btn">
        Logout
    </a>

</div>


<footer>

    <p>
        &copy; 2026 Online Grocery Store. All Rights Reserved.
    </p>

</footer>

</body>

</html>