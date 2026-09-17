<?php

session_start();

require_once "../config/database.php";

$message = "";
$review_message = "";


/* =====================================================
   GET PRODUCT ID
===================================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: index.php");
    exit();

}

$product_id = (int) $_GET["id"];


/* =====================================================
   ADD TO CART
===================================================== */

if (
    $_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST["add_to_cart"])
) {

    if (!isset($_SESSION["user_id"])) {

        header("Location: ../login.php");
        exit();

    }

    $quantity = isset($_POST["quantity"])
        ? (float) $_POST["quantity"]
        : 1;

    if ($quantity <= 0) {
        $quantity = 1;
    }


    /* Get product stock */

    $stock_check = mysqli_prepare(
        $conn,
        "SELECT stock
         FROM products
         WHERE id = ?
         AND status = 'active'"
    );

    mysqli_stmt_bind_param(
        $stock_check,
        "i",
        $product_id
    );

    mysqli_stmt_execute($stock_check);

    $stock_result = mysqli_stmt_get_result(
        $stock_check
    );


    if (mysqli_num_rows($stock_result) == 0) {

        mysqli_stmt_close($stock_check);

        echo "Product not found.";
        exit();

    }


    $stock_data = mysqli_fetch_assoc(
        $stock_result
    );

    mysqli_stmt_close($stock_check);


    /* Check stock */

    if ($stock_data["stock"] <= 0) {

        echo "This product is out of stock.";
        exit();

    }


    if ($quantity > $stock_data["stock"]) {

        $quantity = $stock_data["stock"];

    }


    /* Check existing cart item */

    $check = mysqli_prepare(
        $conn,
        "SELECT id, quantity
         FROM cart
         WHERE user_id = ?
         AND product_id = ?"
    );

    mysqli_stmt_bind_param(
        $check,
        "ii",
        $_SESSION["user_id"],
        $product_id
    );

    mysqli_stmt_execute($check);

    $cart_result = mysqli_stmt_get_result(
        $check
    );


    if (mysqli_num_rows($cart_result) > 0) {

        $cart_item = mysqli_fetch_assoc(
            $cart_result
        );


        /* Replace quantity */

        $update = mysqli_prepare(
            $conn,
            "UPDATE cart
             SET quantity = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "di",
            $quantity,
            $cart_item["id"]
        );

        mysqli_stmt_execute($update);

        mysqli_stmt_close($update);


    } else {

        /* Insert new cart item */

        $insert = mysqli_prepare(
            $conn,
            "INSERT INTO cart
            (user_id, product_id, quantity)
            VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insert,
            "iid",
            $_SESSION["user_id"],
            $product_id,
            $quantity
        );

        mysqli_stmt_execute($insert);

        mysqli_stmt_close($insert);

    }


    mysqli_stmt_close($check);


    /* Go to cart */

    header("Location: ../cart/index.php");
    exit();

}


/* =====================================================
   SUBMIT CUSTOMER REVIEW
===================================================== */

if (
    $_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST["submit_review"])
) {

    /* User must be logged in */

    if (!isset($_SESSION["user_id"])) {

        header("Location: ../login.php");
        exit();

    }


    $user_id = (int) $_SESSION["user_id"];


    $rating = isset($_POST["rating"])
        ? (int) $_POST["rating"]
        : 0;


    $review = isset($_POST["review"])
        ? trim($_POST["review"])
        : "";


    /* Validate rating */

    if ($rating < 1 || $rating > 5) {

        $review_message =
            "Please select a rating between 1 and 5 stars.";

    }

    /* Validate review */

    elseif ($review == "") {

        $review_message =
            "Please write a review.";

    }

    else {

        /* Check whether user already reviewed */

        $check_review = mysqli_prepare(
            $conn,
            "SELECT id
             FROM product_reviews
             WHERE user_id = ?
             AND product_id = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $check_review,
            "ii",
            $user_id,
            $product_id
        );

        mysqli_stmt_execute($check_review);

        $review_result = mysqli_stmt_get_result(
            $check_review
        );


        if (mysqli_num_rows($review_result) > 0) {

            /*
             * User already reviewed.
             * Update existing review.
             */

            $existing_review = mysqli_fetch_assoc(
                $review_result
            );


            $update_review = mysqli_prepare(
                $conn,
                "UPDATE product_reviews
                 SET rating = ?,
                     review = ?,
                     status = 'pending'
                 WHERE id = ?"
            );


            mysqli_stmt_bind_param(
                $update_review,
                "isi",
                $rating,
                $review,
                $existing_review["id"]
            );


            if (mysqli_stmt_execute($update_review)) {

                $review_message =
                    "Successfully submitted your review!";

            } else {

                $review_message =
                    "Unable to submit your review. Please try again.";

            }


            mysqli_stmt_close($update_review);


        } else {

            /*
             * New review.
             * It is stored as pending.
             */

            $insert_review = mysqli_prepare(
                $conn,
                "INSERT INTO product_reviews
                (
                    user_id,
                    product_id,
                    rating,
                    review,
                    status
                )
                VALUES (?, ?, ?, ?, 'pending')"
            );


            mysqli_stmt_bind_param(
                $insert_review,
                "iiis",
                $user_id,
                $product_id,
                $rating,
                $review
            );


            if (mysqli_stmt_execute($insert_review)) {

                $review_message =
                    "Successfully submitted your review!";

            } else {

                $review_message =
                    "Unable to submit your review. Please try again.";

            }


            mysqli_stmt_close($insert_review);

        }


        mysqli_stmt_close($check_review);

    }

}


/* =====================================================
   GET PRODUCT DETAILS
===================================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        products.*,
        categories.name AS category_name
     FROM products
     LEFT JOIN categories
        ON products.category_id = categories.id
     WHERE products.id = ?
     AND products.status = 'active'"
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

    echo "Product not found.";
    exit();

}


$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* =====================================================
   FORMAT PRODUCT UNIT
===================================================== */

$unit = strtolower(
    trim(
        $product["unit"]
    )
);


switch ($unit) {

    case "kg":

        $display_unit = "kg";

        break;


    case "gram":

    case "g":

        $display_unit = "g";

        break;


    case "litre":

    case "liter":

    case "l":

        $display_unit = "L";

        break;


    case "ml":

        $display_unit = "ml";

        break;


    case "dozen":

        $display_unit = "dozen";

        break;


    case "packet":

    case "pack":

        $display_unit = "packet";

        break;


    case "bottle":

        $display_unit = "bottle";

        break;


    case "piece":

        $display_unit = "piece";

        break;


    default:

        $display_unit = $product["unit"];

        break;
}


/* =====================================================
   FORMAT STOCK
===================================================== */

$display_stock = rtrim(
    rtrim(
        number_format(
            (float) $product["stock"],
            2
        ),
        "0"
    ),
    "."
);


/* =====================================================
   GET APPROVED REVIEWS
===================================================== */

$reviews_sql = "
    SELECT
        product_reviews.rating,
        product_reviews.review,
        product_reviews.created_at,
        users.fullname
    FROM product_reviews
    INNER JOIN users
        ON product_reviews.user_id = users.id
    WHERE product_reviews.product_id = ?
    AND product_reviews.status = 'approved'
    ORDER BY product_reviews.id DESC
";


$reviews_stmt = mysqli_prepare(
    $conn,
    $reviews_sql
);

mysqli_stmt_bind_param(
    $reviews_stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($reviews_stmt);

$reviews_result = mysqli_stmt_get_result(
    $reviews_stmt
);


/* =====================================================
   GET AVERAGE RATING
===================================================== */

$rating_sql = "
    SELECT
        AVG(rating) AS average_rating,
        COUNT(*) AS review_count
    FROM product_reviews
    WHERE product_id = ?
    AND status = 'approved'
";


$rating_stmt = mysqli_prepare(
    $conn,
    $rating_sql
);

mysqli_stmt_bind_param(
    $rating_stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($rating_stmt);

$rating_result = mysqli_stmt_get_result(
    $rating_stmt
);


$rating_data = mysqli_fetch_assoc(
    $rating_result
);


if ($rating_data["average_rating"] !== null) {

    $average_rating = round(
        $rating_data["average_rating"],
        1
    );

} else {

    $average_rating = 0;

}


$review_count = (int) $rating_data["review_count"];

mysqli_stmt_close($rating_stmt);

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

        <?php
        echo htmlspecialchars($product["name"]);
        ?>

        - Online Grocery

    </title>


    <link
        rel="stylesheet"
        href="../css/style.css"
    >


    <style>

        /* =================================================
           PRODUCT DETAILS
        ================================================= */

        .details-container {

            max-width: 900px;

            margin: 60px auto;

            padding: 30px;

            background: white;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

            display: flex;

            gap: 40px;

            align-items: center;

        }


        .details-image {

            width: 400px;

            height: 350px;

            object-fit: cover;

            border-radius: 10px;

        }


        .details-info {

            flex: 1;

        }


        .details-info h1 {

            color: #1b5e20;

            margin-bottom: 15px;

        }


        .category {

            color: #777;

            margin-bottom: 15px;

        }


        .description {

            line-height: 1.6;

            margin-bottom: 20px;

        }


        .price {

            font-size: 28px;

            font-weight: bold;

            margin-bottom: 15px;

            color: #222;

        }


        .stock {

            margin-bottom: 20px;

            font-weight: bold;

        }


        .quantity-box {

            margin-bottom: 20px;

        }


        .quantity-box input {

            width: 90px;

            padding: 10px;

            margin-left: 10px;

        }


        .add-cart-btn {

            background: #2e7d32;

            color: white;

            padding: 12px 20px;

            border: none;

            border-radius: 5px;

            cursor: pointer;

            font-size: 16px;

        }


        .add-cart-btn:hover {

            background: #1b5e20;

        }


        .back-btn {

            display: inline-block;

            background: #555;

            color: white;

            padding: 12px 20px;

            text-decoration: none;

            border-radius: 5px;

            margin-top: 15px;

        }


        .back-btn:hover {

            background: #333;

        }


        /* =================================================
           REVIEWS
        ================================================= */

        .reviews-container {

            max-width: 900px;

            margin: 30px auto 60px;

            padding: 30px;

            background: white;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

        }


        .reviews-container h2 {

            color: #1b5e20;

            margin-bottom: 20px;

        }


        .rating-summary {

            background: #f5f7f5;

            padding: 20px;

            border-radius: 8px;

            margin-bottom: 25px;

        }


        .big-rating {

            font-size: 28px;

            font-weight: bold;

            color: #f9a825;

        }


        .stars {

            color: #f9a825;

            font-size: 24px;

            letter-spacing: 2px;

        }


        .review-form {

            background: #f8f9f8;

            padding: 20px;

            border-radius: 8px;

            margin-bottom: 30px;

        }


        .review-form h3 {

            color: #1b5e20;

            margin-top: 0;

        }


        .review-form label {

            display: block;

            font-weight: bold;

            margin-bottom: 8px;

        }


        .review-form select,

        .review-form textarea {

            width: 100%;

            box-sizing: border-box;

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 5px;

            margin-bottom: 15px;

        }


        .review-form textarea {

            min-height: 100px;

            resize: vertical;

        }


        .review-btn {

            background: #2e7d32;

            color: white;

            border: none;

            padding: 11px 18px;

            border-radius: 5px;

            cursor: pointer;

            font-size: 15px;

        }


        .review-btn:hover {

            background: #1b5e20;

        }


        .review-message {

            background: #e8f5e9;

            color: #2e7d32;

            padding: 14px;

            border-radius: 5px;

            margin-top: 20px;

            margin-bottom: 20px;

            font-weight: bold;

        }


        .review-error {

            background: #ffebee;

            color: #c62828;

            padding: 14px;

            border-radius: 5px;

            margin-top: 20px;

            margin-bottom: 20px;

            font-weight: bold;

        }


        .review-item {

            border-bottom: 1px solid #ddd;

            padding: 20px 0;

        }


        .review-item:last-child {

            border-bottom: none;

        }


        .review-name {

            font-weight: bold;

            color: #1b5e20;

        }


        .review-date {

            color: #777;

            font-size: 13px;

            margin-left: 10px;

        }


        .review-text {

            margin-top: 10px;

            line-height: 1.6;

        }


        .login-review {

            background: #fff3cd;

            padding: 15px;

            border-radius: 5px;

            margin-bottom: 25px;

        }


        .login-review a {

            color: #1b5e20;

            font-weight: bold;

        }


        @media (max-width: 700px) {

            .details-container {

                flex-direction: column;

                margin: 30px 15px;

            }


            .details-image {

                width: 100%;

                height: 300px;

            }


            .reviews-container {

                margin: 30px 15px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

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


<!-- =====================================================
     PRODUCT DETAILS
===================================================== -->

<div class="details-container">


    <img
        src="../images/<?php
        echo htmlspecialchars($product["image"]);
        ?>"
        alt="<?php
        echo htmlspecialchars($product["name"]);
        ?>"
        class="details-image"
    >


    <div class="details-info">


        <h1>

            <?php
            echo htmlspecialchars($product["name"]);
            ?>

        </h1>


        <div class="category">

            Category:

            <?php
            echo htmlspecialchars(
                $product["category_name"] ?? "No Category"
            );
            ?>

        </div>


        <p class="description">

            <?php
            echo htmlspecialchars(
                $product["description"]
            );
            ?>

        </p>


        <!-- =================================================
             PRICE WITH CORRECT UNIT
        ================================================= -->

        <div class="price">

            ₹<?php
            echo number_format(
                $product["price"],
                2
            );
            ?>

            /

            <?php
            echo htmlspecialchars(
                $display_unit
            );
            ?>

        </div>


        <!-- =================================================
             STOCK WITH CORRECT UNIT
        ================================================= -->

        <div class="stock">

            Available Stock:

            <?php
            echo $display_stock;
            ?>

            <?php
            echo htmlspecialchars(
                $display_unit
            );
            ?>

        </div>


        <!-- =================================================
             ADD TO CART
        ================================================= -->

        <?php if ((float)$product["stock"] > 0): ?>

            <form
                method="POST"
                action="details.php?id=<?php
                echo $product_id;
                ?>"
            >

                <div class="quantity-box">

                    <label for="quantity">

                        Quantity
                        (<?php
                        echo htmlspecialchars(
                            $display_unit
                        );
                        ?>):

                    </label>


                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        value="1"
                        min="1"
                        max="<?php
                        echo $product["stock"];
                        ?>"
                        step="1"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="add_to_cart"
                    class="add-cart-btn"
                >

                    🛒 Add to Cart

                </button>

            </form>

        <?php else: ?>

            <p style="color:#c62828; font-weight:bold;">

                ❌ Out of Stock

            </p>

        <?php endif; ?>


        <a
            href="index.php"
            class="back-btn"
        >

            ← Back to Products

        </a>


    </div>

</div>


<!-- =====================================================
     CUSTOMER REVIEWS
===================================================== -->

<div class="reviews-container">


    <h2>

        ⭐ Customer Reviews

    </h2>


    <!-- RATING SUMMARY -->

    <?php if ($review_count > 0): ?>

        <div class="rating-summary">

            <div class="big-rating">

                <?php
                echo $average_rating;
                ?>

                / 5

            </div>


            <div class="stars">

                <?php

                $rounded_rating =
                    round($average_rating);

                for (
                    $i = 1;
                    $i <= 5;
                    $i++
                ) {

                    if (
                        $i <= $rounded_rating
                    ) {

                        echo "★";

                    } else {

                        echo "☆";

                    }

                }

                ?>

            </div>


            <div>

                Based on

                <?php
                echo $review_count;
                ?>

                review(s)

            </div>

        </div>

    <?php endif; ?>


    <!-- SUCCESS / ERROR MESSAGE -->

    <?php if ($review_message != ""): ?>

        <?php

        $is_success =
            strpos(
                $review_message,
                "Successfully"
            ) !== false;

        ?>


        <?php if ($is_success): ?>

            <div class="review-message">

                <?php
                echo htmlspecialchars(
                    $review_message
                );
                ?>

            </div>

        <?php else: ?>

            <div class="review-error">

                <?php
                echo htmlspecialchars(
                    $review_message
                );
                ?>

            </div>

        <?php endif; ?>

    <?php endif; ?>


    <!-- REVIEW FORM -->

    <?php if (isset($_SESSION["user_id"])): ?>

        <div class="review-form">

            <h3>

                ✍️ Write a Review

            </h3>


            <form
                method="POST"
                action="details.php?id=<?php
                echo $product_id;
                ?>"
            >

                <label for="rating">

                    Rating

                </label>


                <select
                    name="rating"
                    id="rating"
                    required
                >

                    <option value="">

                        Select Rating

                    </option>


                    <option value="5">

                        ⭐⭐⭐⭐⭐ - Excellent

                    </option>


                    <option value="4">

                        ⭐⭐⭐⭐ - Very Good

                    </option>


                    <option value="3">

                        ⭐⭐⭐ - Good

                    </option>


                    <option value="2">

                        ⭐⭐ - Average

                    </option>


                    <option value="1">

                        ⭐ - Poor

                    </option>

                </select>


                <label for="review">

                    Your Review

                </label>


                <textarea
                    name="review"
                    id="review"
                    placeholder="Write your experience with this product..."
                    maxlength="1000"
                    required
                ></textarea>


                <button
                    type="submit"
                    name="submit_review"
                    class="review-btn"
                >

                    ⭐ Submit Review

                </button>


            </form>

        </div>


    <?php else: ?>

        <div class="login-review">

            Please

            <a href="../login.php">

                login

            </a>

            to write a review.

        </div>

    <?php endif; ?>


    <!-- APPROVED CUSTOMER REVIEWS -->

    <h3>

        💬 Customer Feedback

    </h3>


    <?php if (
        mysqli_num_rows($reviews_result) > 0
    ): ?>


        <?php while (
            $review_data =
            mysqli_fetch_assoc($reviews_result)
        ): ?>

            <div class="review-item">


                <div>

                    <span class="review-name">

                        <?php
                        echo htmlspecialchars(
                            $review_data["fullname"]
                        );
                        ?>

                    </span>


                    <span class="review-date">

                        <?php
                        echo date(
                            "d M Y",
                            strtotime(
                                $review_data["created_at"]
                            )
                        );
                        ?>

                    </span>

                </div>


                <div class="stars">

                    <?php

                    for (
                        $i = 1;
                        $i <= 5;
                        $i++
                    ) {

                        if (
                            $i <=
                            $review_data["rating"]
                        ) {

                            echo "★";

                        } else {

                            echo "☆";

                        }

                    }

                    ?>

                </div>


                <div class="review-text">

                    <?php

                    echo nl2br(
                        htmlspecialchars(
                            $review_data["review"]
                        )
                    );

                    ?>

                </div>


            </div>

        <?php endwhile; ?>


    <?php else: ?>

        <p>

            No approved reviews yet.

        </p>

    <?php endif; ?>


</div>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer>

    <p>

        &copy; 2026 Online Grocery Store.
        All Rights Reserved.

    </p>

</footer>


</body>

</html>