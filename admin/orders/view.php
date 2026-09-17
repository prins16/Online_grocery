
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
   CHECK ORDER ID
========================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$order_id = (int) $_GET["id"];


/* ==========================================
   UPDATE ORDER / PAYMENT STATUS
========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* --------------------------------------
       UPDATE ORDER STATUS
    -------------------------------------- */

    if (isset($_POST["update_order_status"])) {

        $new_status = $_POST["order_status"] ?? "";

        $allowed_statuses = [
            "pending",
            "confirmed",
            "dispatched",
            "out_for_delivery",
            "delivered",
            "cancelled"
        ];

        if (in_array($new_status, $allowed_statuses, true)) {

            $update = mysqli_prepare(
                $conn,
                "UPDATE orders
                 SET order_status = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "si",
                $new_status,
                $order_id
            );

            mysqli_stmt_execute($update);

            mysqli_stmt_close($update);
        }

        header("Location: view.php?id=" . $order_id);
        exit();
    }


    /* --------------------------------------
       UPDATE PAYMENT STATUS
    -------------------------------------- */

    if (isset($_POST["update_payment_status"])) {

        $new_payment_status =
            $_POST["payment_status"] ?? "";

        $allowed_payment_statuses = [
            "pending",
            "paid",
            "failed",
            "refunded"
        ];

        if (
            in_array(
                $new_payment_status,
                $allowed_payment_statuses,
                true
            )
        ) {

            $update_payment = mysqli_prepare(
                $conn,
                "UPDATE orders
                 SET payment_status = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update_payment,
                "si",
                $new_payment_status,
                $order_id
            );

            mysqli_stmt_execute(
                $update_payment
            );

            mysqli_stmt_close(
                $update_payment
            );
        }

        header("Location: view.php?id=" . $order_id);
        exit();
    }
}


/* ==========================================
   GET ORDER INFORMATION
========================================== */

$order_sql = "
    SELECT
        orders.id,
        orders.total_amount,
        orders.discount_amount,
        orders.payment_status,
        orders.order_status,
        orders.order_date,
        orders.notes,

        users.fullname,
        users.email,
        users.phone,

        addresses.address_line,
        addresses.city,
        addresses.state,
        addresses.pincode

    FROM orders

    INNER JOIN users
        ON orders.user_id = users.id

    INNER JOIN addresses
        ON orders.address_id = addresses.id

    WHERE orders.id = ?

    LIMIT 1
";


$order_stmt = mysqli_prepare(
    $conn,
    $order_sql
);

mysqli_stmt_bind_param(
    $order_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute(
    $order_stmt
);

$order_result =
    mysqli_stmt_get_result(
        $order_stmt
    );


if (mysqli_num_rows($order_result) == 0) {

    mysqli_stmt_close(
        $order_stmt
    );

    echo "Order not found.";
    exit();
}


$order =
    mysqli_fetch_assoc(
        $order_result
    );

mysqli_stmt_close(
    $order_stmt
);


/* ==========================================
   GET ORDER ITEMS
========================================== */

$item_sql = "
    SELECT
        order_items.quantity,
        order_items.price,
        order_items.subtotal,
        products.name,
        products.image

    FROM order_items

    INNER JOIN products
        ON order_items.product_id = products.id

    WHERE order_items.order_id = ?
";


$item_stmt = mysqli_prepare(
    $conn,
    $item_sql
);

mysqli_stmt_bind_param(
    $item_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute(
    $item_stmt
);

$item_result =
    mysqli_stmt_get_result(
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
        Order Details - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        .container {

            width: 95%;

            max-width: 1100px;

            margin: 40px auto;

        }


        .box {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.1);

            margin-bottom: 25px;

        }


        h1,
        h2 {

            color: #1b5e20;

        }


        .top-bar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;

        }


        .back-btn {

            background: #777;

            color: white;

            padding: 10px 18px;

            border-radius: 5px;

            text-decoration: none;

        }


        .info-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;

        }


        .info {

            padding: 15px;

            background: #f8f8f8;

            border-radius: 8px;

        }


        .info strong {

            display: block;

            margin-bottom: 6px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

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

        }


        .product-image {

            width: 60px;

            height: 60px;

            object-fit: cover;

            border-radius: 6px;

        }


        .status-form {

            display: flex;

            gap: 10px;

            align-items: center;

        }


        select {

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 5px;

        }


        .update-btn {

            padding: 10px 18px;

            background: #2e7d32;

            color: white;

            border: none;

            border-radius: 5px;

            cursor: pointer;

        }


        .update-btn:hover {

            background: #1b5e20;

        }


        .payment-btn {

            background: #1565c0;

        }


        .payment-btn:hover {

            background: #0d47a1;

        }


        .total {

            text-align: right;

            font-size: 22px;

            font-weight: bold;

            margin-top: 20px;

        }


        .notes {

            background: #fffde7;

            padding: 15px;

            border-radius: 8px;

        }


        .current-status {

            margin-bottom: 15px;

            font-weight: bold;

        }


        @media (max-width: 700px) {

            .info-grid {

                grid-template-columns: 1fr;

            }


            .status-form {

                flex-direction: column;

                align-items: flex-start;

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


<div class="container">


    <!-- TOP BAR -->

    <div class="top-bar">

        <h1>

            📦 Order #<?php

            echo (int) $order["id"];

            ?>

        </h1>


        <a
            href="index.php"
            class="back-btn"
        >

            ← All Orders

        </a>

    </div>


    <!-- ==========================================
         CUSTOMER INFORMATION
    =========================================== -->

    <div class="box">

        <h2>

            👤 Customer Information

        </h2>


        <div class="info-grid">


            <div class="info">

                <strong>

                    Name

                </strong>

                <?php

                echo htmlspecialchars(
                    $order["fullname"]
                );

                ?>

            </div>


            <div class="info">

                <strong>

                    Email

                </strong>

                <?php

                echo htmlspecialchars(
                    $order["email"]
                );

                ?>

            </div>


            <div class="info">

                <strong>

                    Phone

                </strong>

                <?php

                echo htmlspecialchars(
                    $order["phone"]
                );

                ?>

            </div>


            <div class="info">

                <strong>

                    Order Date

                </strong>

                <?php

                echo htmlspecialchars(
                    $order["order_date"]
                );

                ?>

            </div>


        </div>

    </div>


    <!-- ==========================================
         DELIVERY ADDRESS
    =========================================== -->

    <div class="box">

        <h2>

            📍 Delivery Address

        </h2>


        <p>

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

        </p>

    </div>


    <!-- ==========================================
         ORDER STATUS
    =========================================== -->

    <div class="box">

        <h2>

            🚚 Order Status

        </h2>


        <div class="current-status">

            Current Status:

            <?php

            echo ucfirst(
                str_replace(
                    "_",
                    " ",
                    htmlspecialchars(
                        $order["order_status"]
                    )
                )
            );

            ?>

        </div>


        <form
            method="POST"
            class="status-form"
        >


            <select
                name="order_status"
                required
            >

                <option
                    value="pending"
                    <?php

                    if (
                        $order["order_status"]
                        == "pending"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Pending

                </option>


                <option
                    value="confirmed"
                    <?php

                    if (
                        $order["order_status"]
                        == "confirmed"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Confirmed

                </option>


                <option
                    value="dispatched"
                    <?php

                    if (
                        $order["order_status"]
                        == "dispatched"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Dispatched

                </option>


                <option
                    value="out_for_delivery"
                    <?php

                    if (
                        $order["order_status"]
                        == "out_for_delivery"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Out for Delivery

                </option>


                <option
                    value="delivered"
                    <?php

                    if (
                        $order["order_status"]
                        == "delivered"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Delivered

                </option>


                <option
                    value="cancelled"
                    <?php

                    if (
                        $order["order_status"]
                        == "cancelled"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Cancelled

                </option>

            </select>


            <button
                type="submit"
                name="update_order_status"
                class="update-btn"
            >

                Update Order Status

            </button>


        </form>

    </div>


    <!-- ==========================================
         ORDER ITEMS
    =========================================== -->

    <div class="box">

        <h2>

            🛒 Ordered Products

        </h2>


        <div style="overflow-x:auto;">

            <table>

                <thead>

                    <tr>

                        <th>
                            Image
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Quantity
                        </th>

                        <th>
                            Subtotal
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php while (
                    $item =
                    mysqli_fetch_assoc(
                        $item_result
                    )
                ): ?>


                    <tr>


                        <td>

                            <?php if (
                                !empty(
                                    $item["image"]
                                )
                            ): ?>

                                <img
                                    src="../../uploads/products/<?php

                                    echo htmlspecialchars(
                                        $item["image"]
                                    );

                                    ?>"
                                    class="product-image"
                                    alt="Product"
                                >

                            <?php else: ?>

                                No Image

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $item["name"]
                            );

                            ?>

                        </td>


                        <td>

                            ₹<?php

                            echo number_format(
                                $item["price"],
                                2
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo (int)
                                $item["quantity"];

                            ?>

                        </td>


                        <td>

                            ₹<?php

                            echo number_format(
                                $item["subtotal"],
                                2
                            );

                            ?>

                        </td>


                    </tr>


                <?php endwhile; ?>


                </tbody>

            </table>

        </div>


        <div class="total">

            Total:

            ₹<?php

            echo number_format(
                $order["total_amount"],
                2
            );

            ?>

        </div>

    </div>


    <!-- ==========================================
         PAYMENT
    =========================================== -->

    <div class="box">

        <h2>

            💳 Payment

        </h2>


        <div class="current-status">

            Current Payment Status:

            <?php

            echo ucfirst(
                htmlspecialchars(
                    $order["payment_status"]
                )
            );

            ?>

        </div>


        <form
            method="POST"
            class="status-form"
        >


            <select
                name="payment_status"
                required
            >

                <option
                    value="pending"
                    <?php

                    if (
                        $order["payment_status"]
                        == "pending"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Pending

                </option>


                <option
                    value="paid"
                    <?php

                    if (
                        $order["payment_status"]
                        == "paid"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Paid

                </option>


                <option
                    value="failed"
                    <?php

                    if (
                        $order["payment_status"]
                        == "failed"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Failed

                </option>


                <option
                    value="refunded"
                    <?php

                    if (
                        $order["payment_status"]
                        == "refunded"
                    ) {

                        echo "selected";

                    }

                    ?>
                >

                    Refunded

                </option>

            </select>


            <button
                type="submit"
                name="update_payment_status"
                class="update-btn payment-btn"
            >

                Update Payment Status

            </button>


        </form>

    </div>


    <!-- ==========================================
         NOTES
    =========================================== -->

    <?php if (
        !empty(
            $order["notes"]
        )
    ): ?>

        <div class="box">

            <h2>

                📝 Order Notes

            </h2>


            <div class="notes">

                <?php

                echo nl2br(
                    htmlspecialchars(
                        $order["notes"]
                    )
                );

                ?>

            </div>

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
?>