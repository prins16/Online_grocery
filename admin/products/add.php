
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
   GET CATEGORIES
========================================== */

$category_result = mysqli_query(
    $conn,
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);


/* ==========================================
   ADD PRODUCT
========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = (float) $_POST["price"];
    $unit = trim($_POST["unit"]);
    $stock = (float) $_POST["stock"];
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
       CHECK BASIC INFORMATION
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

    } elseif (
        !in_array($unit, $allowed_units)
    ) {

        $error =
            "Invalid product unit.";

    } elseif (
        !in_array($status, ["active", "inactive"])
    ) {

        $error =
            "Invalid product status.";

    } elseif (
        !isset($_FILES["image"]) ||
        $_FILES["image"]["error"] != 0
    ) {

        $error =
            "Please select a product image.";

    } else {


        /* ==========================================
           CHECK IMAGE
        ========================================== */

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


            /* ==========================================
               CREATE IMAGE NAME
            ========================================== */

            $extension = strtolower(
                pathinfo(
                    $_FILES["image"]["name"],
                    PATHINFO_EXTENSION
                )
            );


            $image_name =
                "product_" .
                time() .
                "_" .
                rand(1000, 9999) .
                "." .
                $extension;


            /* ==========================================
               IMAGE UPLOAD LOCATION
            ========================================== */

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

            } else {


                /* ==========================================
                   INSERT PRODUCT
                ========================================== */

                $sql =
                    "INSERT INTO products
                    (
                        category_id,
                        name,
                        description,
                        price,
                        unit,
                        stock,
                        image,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


                $stmt = mysqli_prepare(
                    $conn,
                    $sql
                );


                mysqli_stmt_bind_param(
                    $stmt,
                    "issdsdss",
                    $category_id,
                    $name,
                    $description,
                    $price,
                    $unit,
                    $stock,
                    $image_name,
                    $status
                );


                if (
                    mysqli_stmt_execute($stmt)
                ) {

                    mysqli_stmt_close($stmt);


                    header(
                        "Location: index.php"
                    );

                    exit();

                } else {

                    $error =
                        "Failed to add product: " .
                        mysqli_error($conn);

                    mysqli_stmt_close($stmt);

                }

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
        Add Product - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        .add-container {

            width: 90%;

            max-width: 700px;

            margin: 40px auto;

        }

        .add-box {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

        }

        .add-box h1 {

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

        .form-group input[type="file"] {

            padding: 8px;

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

        .add-btn {

            background: #2e7d32;

        }

        .add-btn:hover {

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

            display: block;

            margin-top: 5px;

            color: #666;

            font-size: 13px;

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


<div class="add-container">

    <div class="add-box">

        <h1>
            ➕ Add New Product
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
                    placeholder="Enter product name"
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
                    placeholder="Enter product description"
                    required
                ></textarea>

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
                    placeholder="Enter price"
                    min="0"
                    step="0.01"
                    required
                >

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

                    <option value="">
                        Select Unit
                    </option>

                    <option value="piece">
                        Piece
                    </option>

                    <option value="kg">
                        Kilogram (kg)
                    </option>

                    <option value="gram">
                        Gram (g)
                    </option>

                    <option value="litre">
                        Litre (L)
                    </option>

                    <option value="ml">
                        Millilitre (ml)
                    </option>

                    <option value="dozen">
                        Dozen
                    </option>

                    <option value="packet">
                        Packet
                    </option>

                    <option value="bottle">
                        Bottle
                    </option>

                </select>

                <small class="unit-help">
                    Example: Rice → kg, Milk → litre, Eggs → dozen
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
                    placeholder="Enter available quantity"
                    min="0"
                    step="0.01"
                    required
                >

                <small class="unit-help">
                    Stock will be measured using the selected unit.
                </small>

            </div>


            <!-- IMAGE -->

            <div class="form-group">

                <label for="image">
                    Product Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                    required
                >

                <small>
                    JPG, JPEG, PNG or WEBP only.
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

                    <option value="active">
                        Active
                    </option>

                    <option value="inactive">
                        Inactive
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn add-btn"
                >
                    ➕ Add Product
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
