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

$error = "";


/* ==========================================
   CHECK PRODUCT ID
========================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: index.php");
    exit();

}

$product_id = (int) $_GET["id"];


/* ==========================================
   GET PRODUCT
========================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM products
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);

    die("Product not found.");

}

$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* ==========================================
   GET CATEGORIES
========================================== */

$category_result = mysqli_query(
    $conn,
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);


/* ==========================================
   UPDATE PRODUCT
========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = (float) $_POST["price"];
    $stock = (float) $_POST["stock"];
    $unit = trim($_POST["unit"]);
    $category_id = (int) $_POST["category_id"];
    $status = $_POST["status"];


    /* ==========================================
       ALLOWED UNITS
    ========================================== */

    $allowed_units = [
        "piece",
        "kg",
        "gram",
        "litre",
        "ml",
        "dozen",
        "packet",
        "bottle"
    ];


    /* ==========================================
       VALIDATION
    ========================================== */

    if (
        $name == "" ||
        $description == "" ||
        $price < 0 ||
        $stock < 0 ||
        $category_id <= 0 ||
        $unit == ""
    ) {

        $error =
            "Please enter valid product information.";

    } elseif (!in_array($unit, $allowed_units)) {

        $error =
            "Please select a valid unit.";

    } elseif (
        !in_array($status, ["active", "inactive"])
    ) {

        $error =
            "Invalid product status.";

    } else {


        /* ==========================================
           KEEP CURRENT IMAGE
        ========================================== */

        $image_name = $product["image"];


        /* ==========================================
           NEW IMAGE UPLOAD
        ========================================== */

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] == 0
        ) {

            $allowed_types = [
                "image/jpeg",
                "image/jpg",
                "image/png",
                "image/webp"
            ];

            $file_type = $_FILES["image"]["type"];


            if (!in_array($file_type, $allowed_types)) {

                $error =
                    "Only JPG, JPEG, PNG and WEBP images are allowed.";

            } else {

                $extension =
                    strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );


                $image_name =
                    "product_" .
                    time() .
                    "_" .
                    $product_id .
                    "." .
                    $extension;


                $upload_path =
                    "../../images/" .
                    $image_name;


                if (
                    !move_uploaded_file(
                        $_FILES["image"]["tmp_name"],
                        $upload_path
                    )
                ) {

                    $error =
                        "Image upload failed.";

                }

            }

        }


        /* ==========================================
           UPDATE DATABASE
        ========================================== */

        if ($error == "") {

            $update_sql =
                "UPDATE products
                 SET
                    category_id = ?,
                    name = ?,
                    description = ?,
                    price = ?,
                    stock = ?,
                    unit = ?,
                    image = ?,
                    status = ?
                 WHERE id = ?";


            $update_stmt = mysqli_prepare(
                $conn,
                $update_sql
            );


            mysqli_stmt_bind_param(
                $update_stmt,
                "issddsssi",
                $category_id,
                $name,
                $description,
                $price,
                $stock,
                $unit,
                $image_name,
                $status,
                $product_id
            );


            if (
                mysqli_stmt_execute($update_stmt)
            ) {

                mysqli_stmt_close($update_stmt);

                header("Location: index.php");

                exit();

            } else {

                $error =
                    "Failed to update product: " .
                    mysqli_error($conn);

                mysqli_stmt_close($update_stmt);

            }

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
        Edit Product - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        .edit-container {

            width: 90%;

            max-width: 700px;

            margin: 40px auto;

        }

        .edit-box {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

        }

        .edit-box h1 {

            color: #1b5e20;

            margin-bottom: 25px;

        }

        .form-group {

            margin-bottom: 18px;

        }

        .form-group label {

            display: block;

            font-weight: bold;

            margin-bottom: 7px;

        }

        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            padding: 11px;

            box-sizing: border-box;

            border: 1px solid #ccc;

            border-radius: 5px;

        }

        .form-group textarea {

            min-height: 120px;

            resize: vertical;

        }

        .current-image {

            width: 150px;

            height: 150px;

            object-fit: cover;

            border-radius: 8px;

            margin-bottom: 10px;

            display: block;

        }

        .buttons {

            display: flex;

            gap: 10px;

            margin-top: 25px;

        }

        .btn {

            padding: 12px 20px;

            border: none;

            border-radius: 5px;

            color: white;

            text-decoration: none;

            cursor: pointer;

        }

        .update-btn {

            background: #2e7d32;

        }

        .update-btn:hover {

            background: #1b5e20;

        }

        .back-btn {

            background: #555;

        }

        .back-btn:hover {

            background: #333;

        }

        .error {

            background: #ffebee;

            color: #c62828;

            padding: 12px;

            border-radius: 5px;

            margin-bottom: 20px;

        }

        .unit-help {

            color: #666;

            font-size: 13px;

            margin-top: 5px;

            display: block;

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


<div class="edit-container">

    <div class="edit-box">


        <h1>
            ✏️ Edit Product
        </h1>


        <?php if ($error != ""): ?>

            <div class="error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- PRODUCT NAME -->

            <div class="form-group">

                <label for="name">
                    Product Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php
                        echo htmlspecialchars(
                            $product["name"]
                        );
                    ?>"
                    required
                >

            </div>


            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                >

                    <option value="">
                        Select Category
                    </option>


                    <?php while (
                        $category =
                        mysqli_fetch_assoc(
                            $category_result
                        )
                    ): ?>

                        <option
                            value="<?php
                                echo $category["id"];
                            ?>"
                            <?php
                            if (
                                $category["id"]
                                ==
                                $product["category_id"]
                            ) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $category["name"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    required
                ><?php
                    echo htmlspecialchars(
                        $product["description"]
                    );
                ?></textarea>

            </div>


            <!-- PRICE -->

            <div class="form-group">

                <label for="price">
                    Price (₹)
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    value="<?php
                        echo $product["price"];
                    ?>"
                    min="0"
                    step="0.01"
                    required
                >

                <small class="unit-help">
                    Enter the price for one selected unit.
                </small>

            </div>


            <!-- UNIT -->

            <div class="form-group">

                <label for="unit">
                    Unit
                </label>

                <select
                    id="unit"
                    name="unit"
                    required
                >

                    <option
                        value="piece"
                        <?php
                        if (
                            $product["unit"] == "piece"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Piece
                    </option>


                    <option
                        value="kg"
                        <?php
                        if (
                            $product["unit"] == "kg"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Kilogram (kg)
                    </option>


                    <option
                        value="gram"
                        <?php
                        if (
                            $product["unit"] == "gram"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Gram (g)
                    </option>


                    <option
                        value="litre"
                        <?php
                        if (
                            $product["unit"] == "litre"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Litre (L)
                    </option>


                    <option
                        value="ml"
                        <?php
                        if (
                            $product["unit"] == "ml"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Millilitre (ml)
                    </option>


                    <option
                        value="dozen"
                        <?php
                        if (
                            $product["unit"] == "dozen"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Dozen
                    </option>


                    <option
                        value="packet"
                        <?php
                        if (
                            $product["unit"] == "packet"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Packet
                    </option>


                    <option
                        value="bottle"
                        <?php
                        if (
                            $product["unit"] == "bottle"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Bottle
                    </option>

                </select>

                <small class="unit-help">
                    Example: Apple → kg, Milk → litre, Eggs → dozen, Bread → piece.
                </small>

            </div>


            <!-- STOCK -->

            <div class="form-group">

                <label for="stock">
                    Available Stock
                </label>

                <input
                    type="number"
                    id="stock"
                    name="stock"
                    value="<?php
                        echo $product["stock"];
                    ?>"
                    min="0"
                    step="0.01"
                    required
                >

                <small class="unit-help">
                    Stock is measured using the selected unit.
                    Example: 20 kg, 15 litre, 50 pieces.
                </small>

            </div>


            <!-- CURRENT IMAGE -->

            <div class="form-group">

                <label>
                    Current Image
                </label>


                <?php if (
                    !empty($product["image"])
                ): ?>

                    <img
                        src="../../images/<?php
                            echo htmlspecialchars(
                                $product["image"]
                            );
                        ?>"
                        alt="Current Product Image"
                        class="current-image"
                    >

                <?php else: ?>

                    <p>
                        No image uploaded.
                    </p>

                <?php endif; ?>

            </div>


            <!-- CHANGE IMAGE -->

            <div class="form-group">

                <label for="image">
                    Change Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    Leave empty if you want to keep the current image.
                </small>

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    required
                >

                    <option
                        value="active"
                        <?php
                        if (
                            $product["status"]
                            == "active"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Active
                    </option>


                    <option
                        value="inactive"
                        <?php
                        if (
                            $product["status"]
                            == "inactive"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn update-btn"
                >
                    💾 Update Product
                </button>


                <a
                    href="index.php"
                    class="btn back-btn"
                >
                    ← Back
                </a>

            </div>


        </form>

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