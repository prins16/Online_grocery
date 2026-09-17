<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $address_line = trim($_POST["address_line"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $pincode = trim($_POST["pincode"]);

    if (
        empty($address_line) ||
        empty($city) ||
        empty($state) ||
        empty($pincode)
    ) {

        $error = "Please fill all address fields.";

    } else {

        /* Check whether user already has an address */

        $check = mysqli_prepare(
            $conn,
            "SELECT COUNT(*) AS total
             FROM addresses
             WHERE user_id = ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "i",
            $user_id
        );

        mysqli_stmt_execute($check);

        $result = mysqli_stmt_get_result($check);

        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($check);

        $is_default = ($row["total"] == 0) ? 1 : 0;


        /* Insert address */

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO addresses
            (
                user_id,
                address_line,
                city,
                state,
                pincode,
                is_default
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssi",
            $user_id,
            $address_line,
            $city,
            $state,
            $pincode,
            $is_default
        );

        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header("Location: ../checkout/index.php");

            exit();

        } else {

            $error = "Unable to save address.";
        }

        mysqli_stmt_close($stmt);
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
        Add Address - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .address-container {

            max-width: 600px;

            margin: 50px auto;

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

        }

        .address-container h1 {

            color: #1b5e20;

            margin-bottom: 25px;

        }

        .form-group {

            margin-bottom: 18px;

        }

        .form-group label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

        }

        .form-group input,
        .form-group textarea {

            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 5px;

            box-sizing: border-box;

        }

        .form-group textarea {

            height: 100px;

            resize: vertical;

        }

        .save-btn {

            width: 100%;

            padding: 13px;

            background: #2e7d32;

            color: white;

            border: none;

            border-radius: 5px;

            font-size: 16px;

            cursor: pointer;

        }

        .save-btn:hover {

            background: #1b5e20;

        }

        .back-btn {

            display: block;

            text-align: center;

            margin-top: 15px;

            color: #2e7d32;

            text-decoration: none;

        }

        .error {

            background: #ffebee;

            color: #c62828;

            padding: 12px;

            border-radius: 5px;

            margin-bottom: 20px;

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

            <li>
                <a href="../index.php">
                    Home
                </a>
            </li>

            <li>
                <a href="../products/index.php">
                    Products
                </a>
            </li>

            <li>
                <a href="../cart/index.php">
                    🛒 Cart
                </a>
            </li>

        </ul>

    </nav>

</header>


<div class="address-container">

    <h1>
        📍 Add Delivery Address
    </h1>


    <?php if ($error != ""): ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <form method="POST">


        <div class="form-group">

            <label>
                Address
            </label>

            <textarea
                name="address_line"
                placeholder="Enter your full address"
                required
            ></textarea>

        </div>


        <div class="form-group">

            <label>
                City
            </label>

            <input
                type="text"
                name="city"
                placeholder="Enter city"
                required
            >

        </div>


        <div class="form-group">

            <label>
                State
            </label>

            <input
                type="text"
                name="state"
                placeholder="Enter state"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Pincode
            </label>

            <input
                type="text"
                name="pincode"
                placeholder="Enter pincode"
                maxlength="10"
                required
            >

        </div>


        <button
            type="submit"
            class="save-btn"
        >

            💾 Save Address

        </button>


    </form>


    <a
        href="../checkout/index.php"
        class="back-btn"
    >

        ← Back to Checkout

    </a>

</div>


</body>

</html>