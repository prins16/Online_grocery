
<?php

session_start();

require_once "../config/database.php";

/* Check login */

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* Get user's wishlist */

$sql = "SELECT
            wishlist.id AS wishlist_id,
            products.id AS product_id,
            products.name,
            products.price,
            products.image,
            products.stock,
            categories.name AS category_name
        FROM wishlist
        INNER JOIN products
            ON wishlist.product_id = products.id
        LEFT JOIN categories
            ON products.category_id = categories.id
        WHERE wishlist.user_id = ?
        ORDER BY wishlist.id DESC";

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
        My Wishlist - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .wishlist-section {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .wishlist-section h1 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 35px;
        }

        .wishlist-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .wishlist-card {
            width: 250px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .wishlist-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .wishlist-card h3 {
            color: #1b5e20;
            margin: 8px 0;
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
        }

        .stock {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .view-btn,
        .remove-btn {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 5px;
            text-decoration: none;
            margin: 4px;
        }

        .view-btn {
            background: #2e7d32;
            color: white;
        }

        .view-btn:hover {
            background: #1b5e20;
        }

        .remove-btn {
            background: #c62828;
            color: white;
        }

        .remove-btn:hover {
            background: #8e0000;
        }

        .empty-wishlist {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .shop-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 11px 20px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
                <a href="profile.php">
                    My Profile
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


<section class="wishlist-section">

    <h1>
        ❤️ My Wishlist
    </h1>


    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="wishlist-container">

            <?php while ($product = mysqli_fetch_assoc($result)): ?>

                <div class="wishlist-card">

                    <img
                        src="../images/<?php echo htmlspecialchars($product["image"]); ?>"
                        alt="<?php echo htmlspecialchars($product["name"]); ?>"
                        class="wishlist-image"
                    >

                    <h3>
                        <?php echo htmlspecialchars($product["name"]); ?>
                    </h3>

                    <div class="category">
                        <?php echo htmlspecialchars($product["category_name"]); ?>
                    </div>

                    <div class="price">
                        ₹<?php echo number_format($product["price"], 2); ?>
                    </div>

                    <div class="stock">
                        Stock: <?php echo $product["stock"]; ?>
                    </div>

                    <a
                        href="../products/details.php?id=<?php echo $product["product_id"]; ?>"
                        class="view-btn"
                    >
                        View Details
                    </a>

                    <a
                        href="remove-wishlist.php?id=<?php echo $product["wishlist_id"]; ?>"
                        class="remove-btn"
                        onclick="return confirm('Remove this product from your wishlist?');"
                    >
                        🗑️ Remove
                    </a>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty-wishlist">

            <h2>
                ❤️ Your Wishlist is Empty
            </h2>

            <p>
                Save your favourite grocery products here.
            </p>

            <a
                href="../products/index.php"
                class="shop-btn"
            >
                🛒 Browse Products
            </a>

        </div>

    <?php endif; ?>

</section>


<footer>

    <p>
        &copy; 2026 Online Grocery Store.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>
?>