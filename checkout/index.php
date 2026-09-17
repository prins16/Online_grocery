
<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$error = "";
$success = "";


/* ==========================================
   GET USER ADDRESSES
========================================== */

$address_sql = "SELECT * FROM addresses
                WHERE user_id = ?
                ORDER BY is_default DESC, id DESC";

$address_stmt = mysqli_prepare($conn, $address_sql);

mysqli_stmt_bind_param(
    $address_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($address_stmt);

$address_result = mysqli_stmt_get_result($address_stmt);


/* ==========================================
   GET CART ITEMS
========================================== */

$cart_sql = "SELECT
                cart.id AS cart_id,
                cart.product_id,
                cart.quantity,
                products.name,
                products.price,
                products.stock,
                products.image
             FROM cart
             INNER JOIN products
             ON cart.product_id = products.id
             WHERE cart.user_id = ?";

$cart_stmt = mysqli_prepare($conn, $cart_sql);

mysqli_stmt_bind_param(
    $cart_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($cart_stmt);

$cart_result = mysqli_stmt_get_result($cart_stmt);


if (mysqli_num_rows($cart_result) == 0) {

    header("Location: ../cart/index.php");

    exit();
}


/* ==========================================
   CALCULATE TOTAL
========================================== */

$cart_items = [];

$total_amount = 0;

while ($item = mysqli_fetch_assoc($cart_result)) {

    $subtotal = $item["price"] * $item["quantity"];

    $item["subtotal"] = $subtotal;

    $cart_items[] = $item;

    $total_amount += $subtotal;
}


/* ==========================================
   PLACE ORDER
========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $address_id = isset($_POST["address_id"])
        ? (int) $_POST["address_id"]
        : 0;

    $payment_method = isset($_POST["payment_method"])
        ? $_POST["payment_method"]
        : "cod";

    $notes = isset($_POST["notes"])
        ? trim($_POST["notes"])
        : "";


    /* Check address */

    $address_check = mysqli_prepare(
        $conn,
        "SELECT id FROM addresses
         WHERE id = ? AND user_id = ?"
    );

    mysqli_stmt_bind_param(
        $address_check,
        "ii",
        $address_id,
        $user_id
    );

    mysqli_stmt_execute($address_check);

    $address_check_result =
        mysqli_stmt_get_result($address_check);


    if (mysqli_num_rows($address_check_result) == 0) {

        $error = "Please select a valid delivery address.";

    } elseif (!in_array(
        $payment_method,
        ["cod", "card", "upi"]
    )) {

        $error = "Invalid payment method.";

    } else {

        /*
         * Start database transaction
         */

        mysqli_begin_transaction($conn);

        try {

            /* ==========================================
               CREATE ORDER
            ========================================== */

            $discount_amount = 0;

            $payment_status = "pending";

            $order_status = "pending";

            $coupon_id = null;


            $order_sql = "INSERT INTO orders
                (
                    user_id,
                    address_id,
                    coupon_id,
                    total_amount,
                    discount_amount,
                    payment_status,
                    order_status,
                    notes
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


            $order_stmt = mysqli_prepare(
                $conn,
                $order_sql
            );


            mysqli_stmt_bind_param(
                $order_stmt,
                "iiiddsss",
                $user_id,
                $address_id,
                $coupon_id,
                $total_amount,
                $discount_amount,
                $payment_status,
                $order_status,
                $notes
            );


            mysqli_stmt_execute($order_stmt);


            $order_id = mysqli_insert_id($conn);


            /* ==========================================
               INSERT ORDER ITEMS
            ========================================== */

            $item_sql = "INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    price,
                    subtotal
                )
                VALUES (?, ?, ?, ?, ?)";


            $item_stmt = mysqli_prepare(
                $conn,
                $item_sql
            );


            foreach ($cart_items as $item) {

                $product_id = $item["product_id"];

                $quantity = $item["quantity"];

                $price = $item["price"];

                $subtotal = $item["subtotal"];


                mysqli_stmt_bind_param(
                    $item_stmt,
                    "iiidd",
                    $order_id,
                    $product_id,
                    $quantity,
                    $price,
                    $subtotal
                );


                mysqli_stmt_execute($item_stmt);


                /* ==========================================
                   REDUCE PRODUCT STOCK
                ========================================== */

                $stock_sql = "UPDATE products
                              SET stock = stock - ?
                              WHERE id = ?
                              AND stock >= ?";


                $stock_stmt = mysqli_prepare(
                    $conn,
                    $stock_sql
                );


                mysqli_stmt_bind_param(
                    $stock_stmt,
                    "iii",
                    $quantity,
                    $product_id,
                    $quantity
                );


                mysqli_stmt_execute($stock_stmt);


                if (mysqli_stmt_affected_rows($stock_stmt) == 0) {

                    throw new Exception(
                        "Not enough stock for "
                        . $item["name"]
                    );
                }


                mysqli_stmt_close($stock_stmt);
            }


            mysqli_stmt_close($item_stmt);


            /* ==========================================
               CREATE PAYMENT
            ========================================== */

            $transaction_id = null;

            $payment_status = "pending";


            $payment_sql = "INSERT INTO payments
                (
                    order_id,
                    payment_method,
                    transaction_id,
                    amount,
                    payment_status
                )
                VALUES (?, ?, ?, ?, ?)";


            $payment_stmt = mysqli_prepare(
                $conn,
                $payment_sql
            );


            mysqli_stmt_bind_param(
                $payment_stmt,
                "issds",
                $order_id,
                $payment_method,
                $transaction_id,
                $total_amount,
                $payment_status
            );


            mysqli_stmt_execute($payment_stmt);


            mysqli_stmt_close($payment_stmt);


            /* ==========================================
               EMPTY CART
            ========================================== */

            $delete_cart = mysqli_prepare(
                $conn,
                "DELETE FROM cart WHERE user_id = ?"
            );


            mysqli_stmt_bind_param(
                $delete_cart,
                "i",
                $user_id
            );


            mysqli_stmt_execute($delete_cart);

            mysqli_stmt_close($delete_cart);


            /* ==========================================
               COMMIT
            ========================================== */

            mysqli_commit($conn);


            /* Go to order success page */

            header(
                "Location: ../order-success.php?id="
                . $order_id
            );

            exit();


        } catch (Exception $e) {

            mysqli_rollback($conn);

            $error = $e->getMessage();
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
        Checkout - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .checkout-container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;

            display: grid;

            grid-template-columns: 2fr 1fr;

            gap: 30px;

        }


        .checkout-box {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            margin-bottom: 20px;

        }


        .checkout-box h2 {

            color: #1b5e20;

            margin-bottom: 20px;

        }


        .address-option {

            border: 1px solid #ddd;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 10px;

        }


        .address-option input {

            margin-right: 10px;

        }


        .payment-option {

            margin: 12px 0;

        }


        .payment-option label {

            margin-left: 8px;

        }


        textarea {

            width: 100%;

            padding: 10px;

            margin-top: 10px;

            min-height: 80px;

            box-sizing: border-box;

        }


        .place-order-btn {

            width: 100%;

            padding: 14px;

            background: #2e7d32;

            color: white;

            border: none;

            border-radius: 5px;

            font-size: 17px;

            cursor: pointer;

        }


        .place-order-btn:hover {

            background: #1b5e20;

        }


        .error {

            background: #ffebee;

            color: #c62828;

            padding: 12px;

            margin-bottom: 20px;

            border-radius: 5px;

        }


        .summary-item {

            display: flex;

            justify-content: space-between;

            padding: 10px 0;

            border-bottom: 1px solid #eee;

        }


        .summary-total {

            display: flex;

            justify-content: space-between;

            font-size: 20px;

            font-weight: bold;

            margin-top: 20px;

        }


        @media (max-width: 800px) {

            .checkout-container {

                grid-template-columns: 1fr;

            }

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


<div class="checkout-container">


    <!-- LEFT SIDE -->

    <div>


        <?php if ($error != ""): ?>

            <div class="error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- ADDRESS -->

            <div class="checkout-box">

                <h2>
                    📍 Delivery Address
                </h2>


                <?php if (mysqli_num_rows($address_result) > 0): ?>


                    <?php while (
                        $address =
                        mysqli_fetch_assoc($address_result)
                    ): ?>

                        <div class="address-option">

                            <label>

                                <input
                                    type="radio"
                                    name="address_id"
                                    value="<?php
                                        echo $address["id"];
                                    ?>"
                                    <?php
                                    if ($address["is_default"] == 1) {
                                        echo "checked";
                                    }
                                    ?>
                                    required
                                >

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $address["address_line"]
                                    );
                                    ?>
                                </strong>

                                <br>

                                <?php
                                echo htmlspecialchars(
                                    $address["city"]
                                );
                                ?>,

                                <?php
                                echo htmlspecialchars(
                                    $address["state"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $address["pincode"]
                                );
                                ?>

                            </label>

                        </div>

                    <?php endwhile; ?>


                <?php else: ?>

                    <p>
                        You don't have a delivery address yet.
                    </p>

                    <p>
                        Please add an address from your profile.
                    </p>
<a href="../user/add-address.php">
    ➕ Add New Address
</a>

                <?php endif; ?>

            </div>


            <!-- PAYMENT -->

            <div class="checkout-box">

                <h2>
                    💳 Payment Method
                </h2>


                <div class="payment-option">

                    <input
                        type="radio"
                        id="cod"
                        name="payment_method"
                        value="cod"
                        checked
                    >

                    <label for="cod">
                        Cash on Delivery
                    </label>

                </div>


                <div class="payment-option">

                    <input
                        type="radio"
                        id="upi"
                        name="payment_method"
                        value="upi"
                    >

                    <label for="upi">
                        UPI
                    </label>

                </div>


                <div class="payment-option">

                    <input
                        type="radio"
                        id="card"
                        name="payment_method"
                        value="card"
                    >

                    <label for="card">
                        Card
                    </label>

                </div>

            </div>


            <!-- NOTES -->

            <div class="checkout-box">

                <h2>
                    📝 Order Notes
                </h2>

                <textarea
                    name="notes"
                    placeholder="Any special instructions?"
                ></textarea>

            </div>


            <?php if (mysqli_num_rows($address_result) > 0): ?>

                <button
                    type="submit"
                    class="place-order-btn"
                >

                    🛍️ Place Order

                </button>

            <?php endif; ?>


        </form>

    </div>


    <!-- RIGHT SIDE -->

    <div>

        <div class="checkout-box">

            <h2>
                🛒 Order Summary
            </h2>


            <?php foreach ($cart_items as $item): ?>

                <div class="summary-item">

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $item["name"]
                        );
                        ?>

                        ×

                        <?php
                        echo $item["quantity"];
                        ?>

                    </span>


                    <span>

                        ₹<?php
                        echo number_format(
                            $item["subtotal"],
                            2
                        );
                        ?>

                    </span>

                </div>

            <?php endforeach; ?>


            <div class="summary-total">

                <span>
                    Total
                </span>

                <span>

                    ₹<?php
                    echo number_format(
                        $total_amount,
                        2
                    );
                    ?>

                </span>

            </div>

        </div>

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
