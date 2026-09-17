
<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$order_id = (int) $_GET["id"];


/* Get order details */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        orders.*,
        addresses.address_line,
        addresses.city,
        addresses.state,
        addresses.pincode
     FROM orders
     INNER JOIN addresses
        ON orders.address_id = addresses.id
     WHERE orders.id = ?
       AND orders.user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Order not found.";
    exit();
}

$order = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* Get order items */

$item_stmt = mysqli_prepare(
    $conn,
    "SELECT
        order_items.quantity,
        order_items.price,
        order_items.subtotal,
        products.name
     FROM order_items
     INNER JOIN products
        ON order_items.product_id = products.id
     WHERE order_items.order_id = ?"
);

mysqli_stmt_bind_param(
    $item_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($item_stmt);

$item_result = mysqli_stmt_get_result($item_stmt);

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
        Order Successful - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <style>

        .success-container {

            max-width: 800px;

            margin: 50px auto;

            padding: 30px;

            background: white;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

        }

        .success-title {

            text-align: center;

            color: #2e7d32;

            margin-bottom: 10px;

        }

        .success-message {

            text-align: center;

            margin-bottom: 30px;

        }

        .order-info {

            background: #f5f5f5;

            padding: 20px;

            border-radius: 8px;

            margin-bottom: 20px;

        }

        .order-info p {

            margin: 8px 0;

        }

        .items-title {

            color: #1b5e20;

            margin-bottom: 15px;

        }

        .order-item {

            display: flex;

            justify-content: space-between;

            padding: 12px 0;

            border-bottom: 1px solid #ddd;

        }

        .total {

            display: flex;

            justify-content: space-between;

            font-size: 22px;

            font-weight: bold;

            margin-top: 20px;

            padding-top: 15px;

            border-top: 2px solid #2e7d32;

        }

        .address-box {

            background: #f9f9f9;

            padding: 15px;

            border-radius: 8px;

            margin-top: 15px;

        }

        .buttons {

            text-align: center;

            margin-top: 30px;

        }

        .btn {

            display: inline-block;

            padding: 12px 20px;

            margin: 5px;

            border-radius: 5px;

            text-decoration: none;

            color: white;

            background: #2e7d32;

        }

        .btn:hover {

            background: #1b5e20;

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
                <a href="index.php">
                    Home
                </a>
            </li>

            <li>
                <a href="products/index.php">
                    Products
                </a>
            </li>

            <li>
                <a href="user/profile.php">
                    My Profile
                </a>
            </li>

            <li>
                <a href="cart/index.php">
                    🛒 Cart
                </a>
            </li>

        </ul>

    </nav>

</header>


<div class="success-container">


    <h1 class="success-title">

        🎉 Order Placed Successfully!

    </h1>


    <p class="success-message">

        Thank you for shopping with Online Grocery.

    </p>


    <!-- ORDER INFORMATION -->

    <div class="order-info">

        <p>

            <strong>
                Order ID:
            </strong>

            #<?php echo $order["id"]; ?>

        </p>


        <p>

            <strong>
                Order Status:
            </strong>

            <?php echo ucfirst(
                $order["order_status"]
            ); ?>

        </p>


        <p>

            <strong>
                Payment:
            </strong>

            <?php echo strtoupper(
                $order["payment_status"]
            ); ?>

        </p>


        <p>

            <strong>
                Payment Method:
            </strong>

            <?php

            $payment_method = "COD";

            $payment_check = mysqli_prepare(
                $conn,
                "SELECT payment_method
                 FROM payments
                 WHERE order_id = ?"
            );

            mysqli_stmt_bind_param(
                $payment_check,
                "i",
                $order_id
            );

            mysqli_stmt_execute($payment_check);

            $payment_result =
                mysqli_stmt_get_result(
                    $payment_check
                );

            if (
                mysqli_num_rows(
                    $payment_result
                ) > 0
            ) {

                $payment_row =
                    mysqli_fetch_assoc(
                        $payment_result
                    );

                $payment_method =
                    strtoupper(
                        $payment_row["payment_method"]
                    );
            }

            mysqli_stmt_close(
                $payment_check
            );

            echo $payment_method;

            ?>

        </p>

    </div>


    <!-- DELIVERY ADDRESS -->

    <h2 class="items-title">

        📍 Delivery Address

    </h2>


    <div class="address-box">

        <?php echo htmlspecialchars(
            $order["address_line"]
        ); ?>

        <br>

        <?php echo htmlspecialchars(
            $order["city"]
        ); ?>,

        <?php echo htmlspecialchars(
            $order["state"]
        ); ?>

        -

        <?php echo htmlspecialchars(
            $order["pincode"]
        ); ?>

    </div>


    <!-- ORDER ITEMS -->

    <h2 class="items-title">

        🛒 Ordered Products

    </h2>


    <?php while (
        $item = mysqli_fetch_assoc(
            $item_result
        )
    ): ?>

        <div class="order-item">

            <span>

                <?php echo htmlspecialchars(
                    $item["name"]
                ); ?>

                ×

                <?php echo $item["quantity"]; ?>

            </span>


            <span>

                ₹<?php echo number_format(
                    $item["subtotal"],
                    2
                ); ?>

            </span>

        </div>

    <?php endwhile; ?>


    <!-- TOTAL -->

    <div class="total">

        <span>
            Total Amount
        </span>

        <span>

            ₹<?php echo number_format(
                $order["total_amount"],
                2
            ); ?>

        </span>

    </div>


    <!-- BUTTONS -->

    <div class="buttons">

        <a
            href="products/index.php"
            class="btn"
        >

            Continue Shopping

        </a>


        <a
            href="user/profile.php"
            class="btn"
        >

            My Profile

        </a>

    </div>


</div>


<footer>

    <p>

        &copy; 2026 Online Grocery Store.
        All Rights Reserved.

    </p>

</footer>


</body>

</html>
