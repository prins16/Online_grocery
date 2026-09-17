
<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


/* Get user's orders */

$sql = "SELECT
            orders.id,
            orders.total_amount,
            orders.discount_amount,
            orders.payment_status,
            orders.order_status,
            orders.order_date
        FROM orders
        WHERE orders.user_id = ?
        ORDER BY orders.id ASC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

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
        My Orders - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .orders-container {

            width: 90%;

            max-width: 1000px;

            margin: 40px auto;

        }

        .orders-container h1 {

            color: #1b5e20;

            margin-bottom: 25px;

        }

        .order-card {

            background: white;

            padding: 20px;

            margin-bottom: 20px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }

        .order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            border-bottom: 1px solid #ddd;

            padding-bottom: 12px;

            margin-bottom: 15px;

        }

        .order-id {

            font-size: 18px;

            font-weight: bold;

            color: #1b5e20;

        }

        .order-row {

            display: flex;

            justify-content: space-between;

            padding: 8px 0;

        }

        .status {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 5px;

            font-size: 14px;

            font-weight: bold;

        }

        .pending {

            background: #fff3cd;

            color: #856404;

        }

        .confirmed {

            background: #d4edda;

            color: #155724;

        }

        .dispatched {

            background: #cce5ff;

            color: #004085;

        }

        .out_for_delivery {

            background: #e2d9f3;

            color: #4a148c;

        }

        .delivered {

            background: #d4edda;

            color: #155724;

        }

        .cancelled {

            background: #f8d7da;

            color: #721c24;

        }

        .view-btn {

            display: inline-block;

            background: #2e7d32;

            color: white;

            padding: 9px 15px;

            border-radius: 5px;

            text-decoration: none;

            margin-top: 12px;

        }

        .view-btn:hover {

            background: #1b5e20;

        }

        .empty-orders {

            background: white;

            padding: 30px;

            text-align: center;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }

        .shop-btn {

            display: inline-block;

            margin-top: 15px;

            background: #2e7d32;

            color: white;

            padding: 10px 18px;

            text-decoration: none;

            border-radius: 5px;

        }

        @media (max-width: 600px) {

            .order-header {

                flex-direction: column;

                align-items: flex-start;

                gap: 10px;

            }

            .order-row {

                flex-direction: column;

                gap: 4px;

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

            <li>
                <a href="profile.php">
                    My Profile
                </a>
            </li>

        </ul>

    </nav>

</header>


<div class="orders-container">

    <h1>
        📦 My Orders
    </h1>


    <?php if (mysqli_num_rows($result) > 0): ?>


        <?php

        /*
         * Display number starts from 1.
         *
         * This does NOT change the real database ID.
         */

        $display_order_number = 1;

        ?>


        <?php while (
            $order = mysqli_fetch_assoc($result)
        ): ?>

            <?php

            $status_class =
                strtolower(
                    str_replace(
                        " ",
                        "-",
                        $order["order_status"]
                    )
                );

            ?>


            <div class="order-card">


                <div class="order-header">

                    <div class="order-id">

                        Order #<?php
                        echo $display_order_number;
                        ?>

                    </div>


                    <span
                        class="status <?php
                            echo htmlspecialchars(
                                $status_class
                            );
                        ?>"
                    >

                        <?php
                        echo ucfirst(
                            str_replace(
                                "_",
                                " ",
                                $order["order_status"]
                            )
                        );
                        ?>

                    </span>

                </div>


                <div class="order-row">

                    <strong>
                        Order Date:
                    </strong>

                    <span>

                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime(
                                $order["order_date"]
                            )
                        );
                        ?>

                    </span>

                </div>


                <div class="order-row">

                    <strong>
                        Payment Status:
                    </strong>

                    <span>

                        <?php
                        echo ucfirst(
                            $order["payment_status"]
                        );
                        ?>

                    </span>

                </div>


                <div class="order-row">

                    <strong>
                        Total Amount:
                    </strong>

                    <strong>

                        ₹<?php
                        echo number_format(
                            $order["total_amount"],
                            2
                        );
                        ?>

                    </strong>

                </div>


                <!--
                    IMPORTANT:
                    The View Order link still uses the
                    REAL database order ID.
                -->

                <a
                    href="order-details.php?id=<?php
                        echo $order["id"];
                    ?>"
                    class="view-btn"
                >

                    View Order

                </a>


            </div>


            <?php

            /*
             * Increase only the displayed number.
             */

            $display_order_number++;

            ?>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="empty-orders">

            <h2>
                No Orders Yet
            </h2>

            <p>
                You haven't placed any orders yet.
            </p>

            <a
                href="../products/index.php"
                class="shop-btn"
            >

                🛒 Start Shopping

            </a>

        </div>


    <?php endif; ?>


</div>


<footer>

    <p>

        &copy; 2026 Online Grocery Store.
        All Rights Reserved.

    </p>

</footer>


</body>

</html>