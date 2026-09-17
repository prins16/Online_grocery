<?php

require_once "../config/database.php";

$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        LEFT JOIN categories
        ON products.category_id = categories.id
        WHERE products.status = 'active'
        ORDER BY products.id DESC";

$result = mysqli_query($conn, $sql);


/* ==========================================
   FORMAT UNIT FOR DISPLAY
========================================== */

function formatUnit($unit)
{
    $unit = strtolower(trim($unit));

    switch ($unit) {

        case "kg":
            return "kg";

        case "gram":
        case "g":
            return "g";

        case "litre":
        case "liter":
        case "l":
            return "L";

        case "ml":
            return "ml";

        case "dozen":
            return "dozen";

        case "packet":
        case "pack":
            return "packet";

        case "bottle":
            return "bottle";

        case "piece":
            return "piece";

        default:
            return $unit;
    }
}


/* ==========================================
   FORMAT STOCK
========================================== */

function formatStock($stock)
{
    return rtrim(
        rtrim(
            number_format(
                (float) $stock,
                2
            ),
            "0"
        ),
        "."
    );
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
        Products - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .products-section {

            padding: 50px;

        }


        .products-section h1 {

            text-align: center;

            color: #1b5e20;

            margin-bottom: 40px;

        }


        .product-container {

            display: flex;

            flex-wrap: wrap;

            justify-content: center;

            gap: 25px;

        }


        .product-card {

            width: 250px;

            background: white;

            border-radius: 10px;

            padding: 20px;

            text-align: center;

            box-shadow:
                0 3px 12px
                rgba(0,0,0,0.1);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .product-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 6px 18px
                rgba(0,0,0,0.15);

        }


        .product-image {

            width: 100%;

            height: 180px;

            object-fit: cover;

            border-radius: 8px;

            margin-bottom: 15px;

        }


        .product-card h3 {

            margin-bottom: 8px;

            color: #1b5e20;

        }


        .category {

            color: #777;

            font-size: 14px;

            margin-bottom: 10px;

        }


        .price {

            font-size: 20px;

            font-weight: bold;

            margin: 10px 0;

            color: #222;

        }


        .price-unit {

            font-size: 15px;

            color: #555;

            font-weight: normal;

        }


        .stock {

            font-size: 14px;

            margin-bottom: 15px;

            color: #555;

        }


        .available {

            color: #2e7d32;

            font-weight: bold;

        }


        .out-of-stock {

            color: #c62828;

            font-weight: bold;

        }


        .view-btn {

            display: inline-block;

            background: #2e7d32;

            color: white;

            padding: 10px 18px;

            border-radius: 5px;

            text-decoration: none;

        }


        .view-btn:hover {

            background: #1b5e20;

        }


        .view-btn.disabled {

            background: #999;

            cursor: not-allowed;

        }


        @media (max-width: 600px) {

            .products-section {

                padding: 25px 15px;

            }

            .product-card {

                width: 85%;

                max-width: 300px;

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
                <a href="index.php">
                    Products
                </a>
            </li>


            <li>
                <a href="../about.php">
                    About Us
                </a>
            </li>


            <li>
                <a href="../contact.php">
                    Contact
                </a>
            </li>


            <li>
                <a href="../login.php">
                    Login
                </a>
            </li>


            <li>
                <a href="../register.php">
                    Register
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


<section class="products-section">


    <h1>
        Our Grocery Products
    </h1>


    <div class="product-container">


        <?php if (
            mysqli_num_rows($result) > 0
        ): ?>


            <?php while (
                $product =
                mysqli_fetch_assoc($result)
            ): ?>


                <?php

                $unit = formatUnit(
                    $product["unit"]
                );


                $stock = formatStock(
                    $product["stock"]
                );

                ?>


                <div class="product-card">


                    <!-- PRODUCT IMAGE -->

                    <img
                        src="../images/<?php
                            echo htmlspecialchars(
                                $product["image"]
                            );
                        ?>"
                        alt="<?php
                            echo htmlspecialchars(
                                $product["name"]
                            );
                        ?>"
                        class="product-image"
                    >


                    <!-- PRODUCT NAME -->

                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $product["name"]
                        );
                        ?>

                    </h3>


                    <!-- CATEGORY -->

                    <div class="category">

                        <?php
                        echo htmlspecialchars(
                            $product["category_name"]
                            ?? "No Category"
                        );
                        ?>

                    </div>


                    <!-- PRICE -->

                    <div class="price">

                        ₹<?php
                        echo number_format(
                            $product["price"],
                            2
                        );
                        ?>

                        <span class="price-unit">

                            /

                         <?php
                            echo htmlspecialchars(
                                $unit
                            );
                            ?>

                        </span>

                    </div>


                    <!-- STOCK -->

                    <div class="stock">


                        <?php if (
                            (float) $product["stock"] > 0
                        ): ?>

                            <span class="available">

                                Available:

                                <?php
                                echo $stock;
                                ?>

                                <?php
                                echo htmlspecialchars(
                                    $unit
                                );
                                ?>

                            </span>


                        <?php else: ?>


                            <span class="out-of-stock">

                                Out of Stock

                            </span>


                        <?php endif; ?>


                    </div>


                    <!-- VIEW DETAILS -->

                    <?php if (
                        (float) $product["stock"] > 0
                    ): ?>

                        <a
                            href="details.php?id=<?php
                                echo $product["id"];
                            ?>"
                            class="view-btn"
                        >

                            View Details

                        </a>


                    <?php else: ?>


                        <span
                            class="view-btn disabled"
                        >

                            Out of Stock

                        </span>


                    <?php endif; ?>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <p>

                No products available.

            </p>


        <?php endif; ?>


    </div>

</section>


<footer>

    <p>

        &copy; 2026 Online Grocery Store.
        All Rights Reserved.

    </p>

</footer>


</body>

</html>