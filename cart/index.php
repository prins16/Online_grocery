
<?php

session_start();

require_once "../config/database.php";


/* ==========================================
   CHECK USER LOGIN
========================================== */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}

$user_id = $_SESSION["user_id"];


/* ==========================================
   GET CART ITEMS
========================================== */

$sql = "SELECT
            cart.id AS cart_id,
            cart.quantity,

            products.id AS product_id,
            products.name,
            products.price,
            products.image,
            products.stock,
            products.unit

        FROM cart

        INNER JOIN products
            ON cart.product_id = products.id

        WHERE cart.user_id = ?

        ORDER BY cart.id DESC";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result(
    $stmt
);


$total = 0;

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
        My Cart - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >


    <style>

        .cart-container {

            width: 90%;

            max-width: 1000px;

            margin: 50px auto;

        }


        .cart-container h1 {

            text-align: center;

            color: #1b5e20;

            margin-bottom: 30px;

        }


        .cart-item {

            background: white;

            padding: 20px;

            margin-bottom: 15px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            display: flex;

            align-items: center;

            gap: 25px;

        }


        .cart-image {

            width: 120px;

            height: 100px;

            object-fit: cover;

            border-radius: 8px;

        }


        .cart-info {

            flex: 1;

        }


        .cart-info h3 {

            color: #1b5e20;

            margin-bottom: 10px;

        }


        .cart-info p {

            margin: 6px 0;

        }


        .product-unit {

            color: #1565c0;

            font-weight: bold;

        }


        .price {

            font-weight: bold;

        }


        .subtotal {

            font-weight: bold;

            color: #1b5e20;

        }


        .cart-total {

            background: white;

            padding: 25px;

            margin-top: 25px;

            border-radius: 10px;

            text-align: right;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }


        .checkout-btn {

            display: inline-block;

            background: #2e7d32;

            color: white;

            padding: 12px 25px;

            margin-top: 15px;

            text-decoration: none;

            border-radius: 5px;

        }


        .checkout-btn:hover {

            background: #1b5e20;

        }


        .empty-cart {

            text-align: center;

            background: white;

            padding: 40px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }


        @media (max-width: 600px) {

            .cart-item {

                flex-direction: column;

                text-align: center;

            }

        }

    </style>

</head>


<body>


<!-- ==========================================
     HEADER
========================================== -->

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
                <a href="../user/profile.php">
                    My Profile
                </a>
            </li>


            <li>
                <a href="index.php">
                    🛒 Cart
                </a>
            </li>


            <li>
                <a href="../logout.php">
                    Logout
                </a>
            </li>

        </ul>

    </nav>

</header>


<!-- ==========================================
     CART
========================================== -->

<div class="cart-container">

    <h1>
        🛒 My Shopping Cart
    </h1>


    <?php if (mysqli_num_rows($result) > 0): ?>


        <!-- ==========================================
             CART ITEMS
        =========================================== -->

        <?php while (
            $item =
            mysqli_fetch_assoc($result)
        ): ?>


            <?php

            $subtotal =
                $item["price"] *
                $item["quantity"];


            $total += $subtotal;


            /* Remove unnecessary decimal
               from stock/quantity */

            $display_quantity =
                rtrim(
                    rtrim(
                        number_format(
                            $item["quantity"],
                            2
                        ),
                        "0"
                    ),
                    "."
                );

            ?>


            <div class="cart-item">


                <!-- PRODUCT IMAGE -->

                <img
                    src="../images/<?php
                        echo htmlspecialchars(
                            $item["image"]
                        );
                    ?>"
                    alt="<?php
                        echo htmlspecialchars(
                            $item["name"]
                        );
                    ?>"
                    class="cart-image"
                >


                <!-- PRODUCT INFORMATION -->

                <div class="cart-info">


                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $item["name"]
                        );
                        ?>

                    </h3>


                    <!-- PRICE -->

                    <p class="price">

                        Price:

                        ₹<?php
                        echo number_format(
                            $item["price"],
                            2
                        );
                        ?>

                        /

                        <span class="product-unit">

                            <?php
                            echo htmlspecialchars(
                                $item["unit"]
                            );
                            ?>

                        </span>

                    </p>


                    <!-- QUANTITY -->

                    <p>

                        Quantity:

                        <strong>
                            <?php
                            echo $display_quantity;
                            ?>
                        </strong>

                        <span class="product-unit">

                            <?php
                            echo htmlspecialchars(
                                $item["unit"]
                            );
                            ?>

                        </span>

                    </p>


                    <!-- SUBTOTAL -->

                    <p class="subtotal">

                        Subtotal:

                        ₹<?php
                        echo number_format(
                            $subtotal,
                            2
                        );
                        ?>

                    </p>


                </div>


            </div>


        <?php endwhile; ?>


        <!-- ==========================================
             CART TOTAL
        =========================================== -->

        <div class="cart-total">


            <h2>

                Total:

                ₹<?php
                echo number_format(
                    $total,
                    2
                );
                ?>

            </h2>


            <a
                href="../checkout/index.php"
                class="checkout-btn"
            >

                Proceed to Checkout

            </a>


        </div>


    <?php else: ?>


        <!-- ==========================================
             EMPTY CART
        =========================================== -->

        <div class="empty-cart">


            <h2>

                Your cart is empty 🛒

            </h2>


            <p>

                Add some products to your cart.

            </p>


            <a
                href="../products/index.php"
                class="checkout-btn"
            >

                Shop Products

            </a>


        </div>


    <?php endif; ?>


</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer>

    <p>

        &copy; 2026 Online Grocery Store.
        All Rights Reserved.

    </p>

</footer>


</body>

</html>
