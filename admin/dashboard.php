
<?php

session_start();

require_once "../config/database.php";


/* ==========================================
   CHECK ADMIN LOGIN
========================================== */

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit();

}

$admin_name = $_SESSION["admin_name"] ?? "Admin";
$admin_email = $_SESSION["admin_email"] ?? "";


/* ==========================================
   GET STATISTICS
========================================== */


/* Total users */

$user_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

$user_data = mysqli_fetch_assoc($user_query);

$total_users = $user_data["total"];


/* Total products */

$product_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);

$product_data = mysqli_fetch_assoc($product_query);

$total_products = $product_data["total"];


/* Total orders */

$order_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM orders"
);

$order_data = mysqli_fetch_assoc($order_query);

$total_orders = $order_data["total"];


/* Pending orders */

$pending_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE order_status = 'pending'"
);

$pending_data = mysqli_fetch_assoc($pending_query);

$pending_orders = $pending_data["total"];


/* Total categories */

$category_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM categories"
);

$category_data = mysqli_fetch_assoc($category_query);

$total_categories = $category_data["total"];


/* ==========================================
   TOTAL SALES
========================================== */

$sales_query = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_amount), 0) AS total
     FROM orders
     WHERE payment_status = 'paid'"
);

$sales_data = mysqli_fetch_assoc($sales_query);

$total_sales = $sales_data["total"];

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
        Admin Dashboard - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        body {
            background: #f5f7f5;
        }

        .admin-container {

            width: 92%;

            max-width: 1200px;

            margin: 40px auto;

        }

        .admin-header {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            margin-bottom: 30px;

        }

        .admin-header h1 {

            color: #1b5e20;

            margin: 0 0 10px 0;

        }

        .admin-header p {

            color: #666;

            margin: 5px 0;

        }

        .dashboard-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;

        }

        .stat-card {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            text-align: center;

        }

        .stat-card .icon {

            font-size: 35px;

            margin-bottom: 10px;

        }

        .stat-card h2 {

            color: #1b5e20;

            margin: 5px 0;

            font-size: 30px;

        }

        .stat-card p {

            color: #666;

            margin: 0;

        }

        .admin-menu {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }

        .admin-menu h2 {

            color: #1b5e20;

            margin-bottom: 20px;

        }

        .menu-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;

        }

        .menu-card {

            display: block;

            padding: 20px;

            background: #f1f8f2;

            border-radius: 8px;

            text-decoration: none;

            color: #1b5e20;

            text-align: center;

            font-weight: bold;

            transition: 0.2s;

        }

        .menu-card:hover {

            background: #dcedc8;

            transform: translateY(-2px);

        }

        .logout {

            display: inline-block;

            margin-top: 20px;

            padding: 10px 20px;

            background: #c62828;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }

        .logout:hover {

            background: #8e0000;

        }

        @media (max-width: 800px) {

            .dashboard-grid {

                grid-template-columns: 1fr 1fr;

            }

            .menu-grid {

                grid-template-columns: 1fr 1fr;

            }

        }

        @media (max-width: 500px) {

            .dashboard-grid {

                grid-template-columns: 1fr;

            }

            .menu-grid {

                grid-template-columns: 1fr;

            }

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


    <!-- =====================================
         ADMIN INFORMATION
    ====================================== -->

    <div class="admin-header">

        <h1>

            👨‍💼 Welcome,

            <?php

            echo htmlspecialchars(
                $admin_name
            );

            ?>!

        </h1>


        <p>

            Admin Email:

            <?php

            echo htmlspecialchars(
                $admin_email
            );

            ?>

        </p>


        <a
            href="logout.php"
            class="logout"
        >

            🚪 Logout

        </a>

    </div>


    <!-- =====================================
         STATISTICS
    ====================================== -->

    <div class="dashboard-grid">


        <!-- TOTAL USERS -->

        <div class="stat-card">

            <div class="icon">
                👥
            </div>

            <h2>

                <?php

                echo $total_users;

                ?>

            </h2>

            <p>
                Total Users
            </p>

        </div>


        <!-- TOTAL PRODUCTS -->

        <div class="stat-card">

            <div class="icon">
                📦
            </div>

            <h2>

                <?php

                echo $total_products;

                ?>

            </h2>

            <p>
                Total Products
            </p>

        </div>


        <!-- TOTAL ORDERS -->

        <div class="stat-card">

            <div class="icon">
                🛒
            </div>

            <h2>

                <?php

                echo $total_orders;

                ?>

            </h2>

            <p>
                Total Orders
            </p>

        </div>


        <!-- PENDING ORDERS -->

        <div class="stat-card">

            <div class="icon">
                ⏳
            </div>

            <h2>

                <?php

                echo $pending_orders;

                ?>

            </h2>

            <p>
                Pending Orders
            </p>

        </div>


        <!-- TOTAL CATEGORIES -->

        <div class="stat-card">

            <div class="icon">
                🗂️
            </div>

            <h2>

                <?php

                echo $total_categories;

                ?>

            </h2>

            <p>
                Categories
            </p>

        </div>


        <!-- TOTAL SALES -->

        <div class="stat-card">

            <div class="icon">
                💰
            </div>

            <h2>

                ₹<?php

                echo number_format(
                    $total_sales,
                    2
                );

                ?>

            </h2>

            <p>
                Total Sales
            </p>

        </div>

    </div>


    <!-- =====================================
         ADMIN MANAGEMENT
    ====================================== -->

    <div class="admin-menu">

        <h2>

            ⚙️ Admin Management

        </h2>


        <div class="menu-grid">


            <!-- PRODUCTS -->

            <a
                href="products/index.php"
                class="menu-card"
            >

                📦<br>

                Manage Products

            </a>


            <!-- CATEGORIES -->

            <a
                href="categories/index.php"
                class="menu-card"
            >

                🗂️<br>

                Manage Categories

            </a>


            <!-- USERS -->

            <a
                href="users/index.php"
                class="menu-card"
            >

                👥<br>

                Manage Users

            </a>


            <!-- ORDERS -->

            <a
                href="orders/index.php"
                class="menu-card"
            >

                🛒<br>

                Manage Orders

            </a>


            <!-- COUPONS -->

            <a
                href="coupons/index.php"
                class="menu-card"
            >

                🎟️<br>

                Manage Coupons

            </a>


            <!-- PRODUCT REVIEWS -->

            <a
                href="product-reviews/index.php"
                class="menu-card"
            >

                ⭐<br>

                Product Reviews

            </a>


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
