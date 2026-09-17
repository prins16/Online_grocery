
<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];


/* Check order ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int) $_GET["id"];


/* Get order information */

$order_sql = "SELECT
                orders.*,
                addresses.address_line,
                addresses.city,
                addresses.state,
                addresses.pincode
              FROM orders
              INNER JOIN addresses
                ON orders.address_id = addresses.id
              WHERE orders.id = ?
              AND orders.user_id = ?";

$order_stmt = mysqli_prepare(
    $conn,
    $order_sql
);

mysqli_stmt_bind_param(
    $order_stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result(
    $order_stmt
);


if (mysqli_num_rows($order_result) == 0) {

    echo "Order not found.";

    exit();
}

$order = mysqli_fetch_assoc(
    $order_result
);

mysqli_stmt_close($order_stmt);


/* Get ordered products */

$item_sql = "SELECT
                order_items.quantity,
                order_items.price,
                order_items.subtotal,
                products.name,
                products.image
             FROM order_items
             INNER JOIN products
                ON order_items.product_id = products.id
             WHERE order_items.order_id = ?";

$item_stmt = mysqli_prepare(
    $conn,
    $item_sql
);

mysqli_stmt_bind_param(
    $item_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($item_stmt);

$item_result = mysqli_stmt_get_result(
    $item_stmt
);

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
        Order #<?php echo $order_id; ?> - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .details-container {

            width: 90%;

            max-width: 1000px;

            margin: 40px auto;

        }


        .details-box {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            margin-bottom: 20px;

        }


        .details-box h2 {

            color: #1b5e20;

            margin-bottom: 20px;

        }


        .order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            flex-wrap: wrap;

            gap: 10px;

        }


        .order-id {

            font-size: 22px;

            font-weight: bold;

            color: #1b5e20;

        }


        .status {

            padding: 7px 12px;

            border-radius: 5px;

            font-weight: bold;

            background: #fff3cd;

            color: #856404;

        }


        .address {

            line-height: 1.7;

        }


        .item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 15px 0;

            border-bottom: 1px solid #eee;

        }


        .item-left {

            display: flex;

            align-items: center;

            gap: 15px;

        }


        .item-image {

            width: 70px;

            height: 70px;

            object-fit: cover;

            border-radius: 8px;

        }


        .item-name {

            font-weight: bold;

        }


        .item-price {

            color: #555;

            margin-top: 5px;

        }


        .item-total {

            font-weight: bold;

        }


        .total-row {

            display: flex;

            justify-content: space-between;

            font-size: 22px;

            font-weight: bold;

            margin-top: 20px;

            padding-top: 15px;

            border-top: 2px solid #2e7d32;

        }


        .info-row {

            display: flex;

            justify-content: space-between;

            padding: 8px 0;

        }


        .back-btn {

            display: inline-block;

            background: #2e7d32;

            color: white;

            padding: 12px 20px;

            border-radius: 5px;

            text-decoration: none;

            margin-top: 10px;

        }


        .back-btn:hover {

            background: #1b5e20;

        }


        @media (max-width: 600px) {

            .item {

                flex-direction: column;

                align-items: flex-start;

                gap: 10px;

            }

            .item-left {

                width: 100%;

            }

            .item-total {

                align-self: flex-end;

            }

            .info-row {

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


<div class="details-container">


    <!-- ORDER INFORMATION -->

    <div class="details-box">

        <div class="order-header">

            <div class="order-id">

                Order #<?php echo $order["id"]; ?>

            </div>


            <div class="status">

                <?php

                echo ucfirst(
                    str_replace(
                        "_",
                        " ",
                        $order["order_status"]
                    )
                );

                ?>

            </div>

        </div>


        <br>


        <div class="info-row">

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


        <div class="info-row">

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


        <div class="info-row">

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

    </div>


    <!-- DELIVERY ADDRESS -->

    <div class="details-box">

        <h2>
            📍 Delivery Address
        </h2>


        <div class="address">

            <?php

            echo htmlspecialchars(
                $order["address_line"]
            );

            ?>

            <br>

            <?php

            echo htmlspecialchars(
                $order["city"]
            );

            ?>,

            <?php

            echo htmlspecialchars(
                $order["state"]
            );

            ?>

            -

            <?php

            echo htmlspecialchars(
                $order["pincode"]
            );

            ?>

        </div>

    </div>


    <!-- ORDER ITEMS -->

    <div class="details-box">

        <h2>
            🛒 Ordered Products
        </h2>


        <?php if (
            mysqli_num_rows(
                $item_result
            ) > 0
        ): ?>


            <?php while (
                $item =
                    mysqli_fetch_assoc(
                        $item_result
                    )
            ): ?>


                <div class="item">


                    <div class="item-left">


                        <?php if (
                            !empty(
                                $item["image"]
                            )
                        ): ?>

                            <img
                                src="../images/<?php
                                    echo htmlspecialchars(
                                        $item["image"]
                                    );
                                ?>"
                                class="item-image"
                                alt="<?php
                                    echo htmlspecialchars(
                                        $item["name"]
                                    );
                                ?>"
                            >

                        <?php endif; ?>


                        <div>

                            <div class="item-name">

                                <?php

                                echo htmlspecialchars(
                                    $item["name"]
                                );

                                ?>

                            </div>


                            <div class="item-price">

                                ₹<?php

                                echo number_format(
                                    $item["price"],
                                    2
                                );

                                ?>

                                ×

                                <?php

                                echo $item["quantity"];

                                ?>

                            </div>

                        </div>


                    </div>


                    <div class="item-total">

                        ₹<?php

                        echo number_format(
                            $item["subtotal"],
                            2
                        );

                        ?>

                    </div>


                </div>


            <?php endwhile; ?>


        <?php else: ?>

            <p>
                No products found for this order.
            </p>

        <?php endif; ?>


        <div class="total-row">

            <span>
                Total
            </span>

            <span>

                ₹<?php

                echo number_format(
                    $order["total_amount"],
                    2
                );

                ?>

            </span>

        </div>

    </div>


    <!-- NOTES -->

    <?php if (
        !empty(
            $order["notes"]
        )
    ): ?>

        <div class="details-box">

            <h2>
                📝 Order Notes
            </h2>

            <p>

                <?php

                echo nl2br(
                    htmlspecialchars(
                        $order["notes"]
                    )
                );

                ?>

            </p>

        </div>

    <?php endif; ?>


    <a
        href="orders.php"
        class="back-btn"
    >

        ← Back to My Orders

    </a>


</div>


<footer>

    <p>

        &copy; 2026 Online Grocery Store.
        All Rights Reserved.

    </p>

</footer>


</body>

</html>
?>