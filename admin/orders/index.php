
<?php

session_start();

require_once "../../config/database.php";

/* Check admin login */

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}


/* Get all orders */

$sql = "SELECT
            orders.id,
            orders.total_amount,
            orders.discount_amount,
            orders.payment_status,
            orders.order_status,
            orders.order_date,
            users.fullname,
            users.email
        FROM orders
        INNER JOIN users
            ON orders.user_id = users.id
        ORDER BY orders.id ASC";

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
        Orders Management - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h1 {
            color: #1b5e20;
            margin: 0;
        }

        .back-btn {
            background: #777;
            color: white;
            padding: 10px 18px;
            border-radius: 5px;
            text-decoration: none;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
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

        tr:hover {
            background: #f5f5f5;
        }

        .status {
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .dispatched {
            background: #cce5ff;
            color: #004085;
        }

        .out_for_delivery {
            background: #e2d9f3;
            color: #4a148c;
        }

        .delivered {
            background: #d4edda;
            color: #155724;
        }

        .cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .paid {
            color: #2e7d32;
            font-weight: bold;
        }

        .failed {
            color: #c62828;
            font-weight: bold;
        }

        .view-btn {
            background: #1976d2;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .view-btn:hover {
            background: #0d47a1;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        @media (max-width: 700px) {

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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

    <div class="box">


        <div class="top-bar">

            <h1>
                📦 Orders Management
            </h1>

            <a
                href="../dashboard.php"
                class="back-btn"
            >
                ← Dashboard
            </a>

        </div>


        <div class="table-container">


            <?php if (
                mysqli_num_rows($result) > 0
            ): ?>


                <table>

                    <thead>

                        <tr>

                            <th>
                                Order ID
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Payment
                            </th>

                            <th>
                                Order Status
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php

                        /*
                         * This is the DISPLAY number.
                         * It starts from 1 for the first order.
                         *
                         * It does NOT change the real database ID.
                         */

                        $display_order_number = 1;

                        ?>


                        <?php while (
                            $order =
                            mysqli_fetch_assoc($result)
                        ): ?>


                            <tr>


                                <!-- DISPLAY ORDER NUMBER -->

                                <td>

                                    #<?php
                                    echo $display_order_number;
                                    ?>

                                </td>


                                <!-- CUSTOMER -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $order["fullname"]
                                    );
                                    ?>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $order["email"]
                                    );
                                    ?>

                                </td>


                                <!-- TOTAL -->

                                <td>

                                    ₹<?php

                                    echo number_format(
                                        $order["total_amount"],
                                        2
                                    );

                                    ?>

                                </td>


                                <!-- PAYMENT -->

                                <td>


                                    <?php if (
                                        $order["payment_status"]
                                        == "paid"
                                    ): ?>


                                        <span class="paid">

                                            Paid

                                        </span>


                                    <?php elseif (
                                        $order["payment_status"]
                                        == "failed"
                                    ): ?>


                                        <span class="failed">

                                            Failed

                                        </span>


                                    <?php else: ?>


                                        Pending


                                    <?php endif; ?>


                                </td>


                                <!-- ORDER STATUS -->

                                <td>


                                    <span
                                        class="status
                                        <?php

                                        echo strtolower(
                                            str_replace(
                                                " ",
                                                "_",
                                                $order["order_status"]
                                            )
                                        );

                                        ?>"
                                    >


                                        <?php

                                        echo htmlspecialchars(
                                            ucfirst(
                                                str_replace(
                                                    "_",
                                                    " ",
                                                    $order["order_status"]
                                                )
                                            )
                                        );

                                        ?>


                                    </span>


                                </td>


                                <!-- DATE -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $order["order_date"]
                                    );

                                    ?>

                                </td>


                                <!-- VIEW -->

                                <td>


                                    <a
                                        href="view.php?id=<?php echo $order["id"]; ?>"
                                        class="view-btn"
                                    >

                                        👁️ View

                                    </a>


                                </td>


                            </tr>


                            <?php

                            /*
                             * Increase only the displayed number.
                             */

                            $display_order_number++;

                            ?>


                        <?php endwhile; ?>


                    </tbody>

                </table>


            <?php else: ?>


                <div class="empty">

                    No orders found.

                </div>


            <?php endif; ?>


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
