
<?php

session_start();

require_once "../../config/database.php";

/* ==========================================
   CHECK ADMIN LOGIN
========================================== */

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../login.php");
    exit();

}


/* ==========================================
   GET ALL PRODUCTS
========================================== */

$sql = "SELECT
            products.id,
            products.name,
            products.description,
            products.price,
            products.stock,
            products.unit,
            products.image,
            products.status,
            categories.name AS category_name
        FROM products
        LEFT JOIN categories
        ON products.category_id = categories.id
        ORDER BY products.id ASC";

$result = mysqli_query($conn, $sql);

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
        Manage Products - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        .admin-container {

            width: 95%;

            max-width: 1300px;

            margin: 40px auto;

        }

        .page-header {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            margin-bottom: 25px;

        }

        .page-header h1 {

            color: #1b5e20;

            margin: 0 0 15px 0;

        }

        .top-buttons {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

        }

        .btn {

            display: inline-block;

            padding: 10px 18px;

            border-radius: 5px;

            text-decoration: none;

            color: white;

            border: none;

            cursor: pointer;

        }

        .btn-add {

            background: #2e7d32;

        }

        .btn-add:hover {

            background: #1b5e20;

        }

        .btn-dashboard {

            background: #555;

        }

        .btn-dashboard:hover {

            background: #333;

        }

        .products-box {

            background: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            overflow-x: auto;

        }

        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 1050px;

        }

        th {

            background: #2e7d32;

            color: white;

            padding: 12px;

            text-align: left;

        }

        td {

            padding: 12px;

            border-bottom: 1px solid #ddd;

            vertical-align: middle;

        }

        tr:hover {

            background: #f5f5f5;

        }

        .product-image {

            width: 70px;

            height: 70px;

            object-fit: cover;

            border-radius: 8px;

        }

        .status-active {

            color: #2e7d32;

            font-weight: bold;

        }

        .status-inactive {

            color: #c62828;

            font-weight: bold;

        }

        .stock-low {

            color: #c62828;

            font-weight: bold;

        }

        .stock-good {

            color: #2e7d32;

            font-weight: bold;

        }

        .unit {

            font-weight: bold;

            color: #1565c0;

        }

        .price-unit {

            font-weight: bold;

        }

        .action-btn {

            display: inline-block;

            padding: 7px 12px;

            margin: 2px;

            border-radius: 4px;

            color: white;

            text-decoration: none;

            font-size: 14px;

        }

        .edit-btn {

            background: #1565c0;

        }

        .delete-btn {

            background: #c62828;

        }

        .edit-btn:hover {

            background: #0d47a1;

        }

        .delete-btn:hover {

            background: #8e0000;

        }

        .no-products {

            text-align: center;

            padding: 30px;

            color: #777;

        }

    </style>

</head>


<body>


<header>

    <nav class="navbar">

        <div class="logo">
            Online Grocery - Admin
        </div>

    </nav>

</header>


<div class="admin-container">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <h1>
            📦 Manage Products
        </h1>

        <div class="top-buttons">

            <a
                href="../dashboard.php"
                class="btn btn-dashboard"
            >
                ← Dashboard
            </a>

            <a
                href="add.php"
                class="btn btn-add"
            >
                ➕ Add Product
            </a>

        </div>

    </div>


    <!-- PRODUCTS TABLE -->

    <div class="products-box">

        <?php if (mysqli_num_rows($result) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Image
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Price / Unit
                        </th>

                        <th>
                            Stock
                        </th>

                        <th>
                            Unit
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php while ($product = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <!-- ID -->

                        <td>
                            <?php
                            echo $product["id"];
                            ?>
                        </td>


                        <!-- IMAGE -->

                        <td>

                            <?php if (!empty($product["image"])): ?>

                                <img
                                    src="../../images/<?php
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

                            <?php else: ?>

                                No Image

                            <?php endif; ?>

                        </td>


                        <!-- PRODUCT -->

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $product["name"]
                                );
                                ?>
                            </strong>

                            <br>

                            <small>

                                <?php

                                $description =
                                    $product["description"];

                                if (
                                    strlen($description) > 60
                                ) {

                                    echo htmlspecialchars(
                                        substr(
                                            $description,
                                            0,
                                            60
                                        )
                                    ) . "...";

                                } else {

                                    echo htmlspecialchars(
                                        $description
                                    );

                                }

                                ?>

                            </small>

                        </td>


                        <!-- CATEGORY -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $product["category_name"]
                                ?? "No Category"
                            );

                            ?>

                        </td>


                        <!-- PRICE / UNIT -->

                        <td>

                            <span class="price-unit">

                                ₹<?php

                                echo number_format(
                                    $product["price"],
                                    2
                                );

                                ?>

                                /

                                <?php

                                echo htmlspecialchars(
                                    $product["unit"]
                                );

                                ?>

                            </span>

                        </td>


                        <!-- STOCK -->

                        <td>

                            <?php if (
                                $product["stock"] <= 5
                            ): ?>

                                <span class="stock-low">

                                    <?php
                                    echo rtrim(
                                        rtrim(
                                            number_format(
                                                $product["stock"],
                                                2
                                            ),
                                            "0"
                                        ),
                                        "."
                                    );
                                    ?>

                                    <?php
                                    echo htmlspecialchars(
                                        $product["unit"]
                                    );
                                    ?>

                                    ⚠️

                                </span>

                            <?php else: ?>

                                <span class="stock-good">

                                    <?php
                                    echo rtrim(
                                        rtrim(
                                            number_format(
                                                $product["stock"],
                                                2
                                            ),
                                            "0"
                                        ),
                                        "."
                                    );
                                    ?>

                                    <?php
                                    echo htmlspecialchars(
                                        $product["unit"]
                                    );
                                    ?>

                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- UNIT -->

                        <td>

                            <span class="unit">

                                <?php

                                echo htmlspecialchars(
                                    $product["unit"]
                                );

                                ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php if (
                                $product["status"] == "active"
                            ): ?>

                                <span class="status-active">
                                    Active
                                </span>

                            <?php else: ?>

                                <span class="status-inactive">
                                    Inactive
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- ACTIONS -->

                        <td>

                            <a
                                href="edit.php?id=<?php
                                    echo $product["id"];
                                ?>"
                                class="action-btn edit-btn"
                            >
                                ✏️ Edit
                            </a>


                            <a
                                href="delete.php?id=<?php
                                    echo $product["id"];
                                ?>"
                                class="action-btn delete-btn"
                                onclick="return confirm(
                                    'Are you sure you want to delete this product?'
                                );"
                            >
                                🗑️ Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="no-products">

                <h2>
                    No Products Found
                </h2>

                <p>
                    Click "Add Product" to add your first product.
                </p>

            </div>

        <?php endif; ?>

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