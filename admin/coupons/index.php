
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
   GET COUPONS
========================================== */

$sql = "SELECT
            id,
            code,
            discount_type,
            discount_value,
            min_order,
            expiry_date,
            status,
            created_at
        FROM coupons
        ORDER BY id ASC";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Database Error: "
        . mysqli_error($conn)
    );

}


/* ==========================================
   SERIAL NUMBER
   This is only for display.
   Database IDs are NOT changed.
========================================== */

$serial_no = 1;

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
        Manage Coupons - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        body {

            background: #f5f7f5;

        }


        .admin-container {

            width: 95%;

            max-width: 1200px;

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

            margin: 0 0 10px 0;

        }


        .page-header p {

            color: #666;

            margin: 0;

        }


        .header-buttons {

            margin-top: 18px;

        }


        .btn {

            display: inline-block;

            padding: 10px 18px;

            border-radius: 5px;

            text-decoration: none;

            color: white;

            margin-right: 8px;

        }


        .add-btn {

            background: #2e7d32;

        }


        .add-btn:hover {

            background: #1b5e20;

        }


        .back-btn {

            background: #666;

        }


        .back-btn:hover {

            background: #444;

        }


        .table-box {

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

            min-width: 1000px;

        }


        th {

            background: #2e7d32;

            color: white;

            padding: 13px;

            text-align: left;

        }


        td {

            padding: 12px;

            border-bottom: 1px solid #ddd;

        }


        tr:hover {

            background: #f1f8f2;

        }


        .coupon-code {

            font-weight: bold;

            color: #1b5e20;

        }


        .percentage {

            color: #1565c0;

            font-weight: bold;

        }


        .fixed {

            color: #6a1b9a;

            font-weight: bold;

        }


        .status-active {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 15px;

            background: #e8f5e9;

            color: #2e7d32;

            font-weight: bold;

        }


        .status-inactive {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 15px;

            background: #ffebee;

            color: #c62828;

            font-weight: bold;

        }


        .action-btn {

            display: inline-block;

            padding: 7px 11px;

            margin: 2px;

            border-radius: 5px;

            text-decoration: none;

            color: white;

            font-size: 14px;

        }


        .edit-btn {

            background: #f9a825;

        }


        .edit-btn:hover {

            background: #f57f17;

        }


        .delete-btn {

            background: #c62828;

        }


        .delete-btn:hover {

            background: #8e0000;

        }


        .no-coupons {

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


    <!-- ==========================================
         PAGE HEADER
    ========================================== -->

    <div class="page-header">

        <h1>

            🎟️ Manage Coupons

        </h1>


        <p>

            Create and manage discount coupons.

        </p>


        <div class="header-buttons">


            <a
                href="add.php"
                class="btn add-btn"
            >

                ➕ Add Coupon

            </a>


            <a
                href="../dashboard.php"
                class="btn back-btn"
            >

                ← Dashboard

            </a>


        </div>

    </div>


    <!-- ==========================================
         COUPONS TABLE
    ========================================== -->

    <div class="table-box">


        <?php if (
            mysqli_num_rows($result) > 0
        ): ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            No.
                        </th>

                        <th>
                            Coupon Code
                        </th>

                        <th>
                            Discount
                        </th>

                        <th>
                            Minimum Order
                        </th>

                        <th>
                            Expiry Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created At
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php while (
                    $coupon =
                    mysqli_fetch_assoc($result)
                ): ?>


                    <tr>


                        <!-- SERIAL NUMBER -->

                        <td>

                            <?php

                            echo $serial_no;

                            ?>

                        </td>


                        <!-- COUPON CODE -->

                        <td class="coupon-code">

                            <?php

                            echo htmlspecialchars(
                                $coupon["code"]
                            );

                            ?>

                        </td>


                        <!-- DISCOUNT -->

                        <td>


                            <?php if (
                                $coupon["discount_type"]
                                == "percentage"
                            ): ?>


                                <span
                                    class="percentage"
                                >

                                    <?php

                                    echo number_format(
                                        $coupon["discount_value"],
                                        2
                                    );

                                    ?>%

                                </span>


                            <?php else: ?>


                                <span
                                    class="fixed"
                                >

                                    ₹<?php

                                    echo number_format(
                                        $coupon["discount_value"],
                                        2
                                    );

                                    ?>

                                </span>


                            <?php endif; ?>


                        </td>


                        <!-- MINIMUM ORDER -->

                        <td>

                            ₹<?php

                            echo number_format(
                                $coupon["min_order"],
                                2
                            );

                            ?>

                        </td>


                        <!-- EXPIRY -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $coupon["expiry_date"]
                            );

                            ?>

                        </td>


                        <!-- STATUS -->

                        <td>


                            <?php if (
                                $coupon["status"]
                                == "active"
                            ): ?>


                                <span
                                    class="status-active"
                                >

                                    Active

                                </span>


                            <?php else: ?>


                                <span
                                    class="status-inactive"
                                >

                                    Inactive

                                </span>


                            <?php endif; ?>


                        </td>


                        <!-- CREATED -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $coupon["created_at"]
                            );

                            ?>

                        </td>


                        <!-- ACTIONS -->

                        <td>


                            <a
                                href="edit.php?id=<?php echo $coupon["id"]; ?>"
                                class="action-btn edit-btn"
                            >

                                ✏️ Edit

                            </a>


                            <a
                                href="delete.php?id=<?php echo $coupon["id"]; ?>"
                                class="action-btn delete-btn"
                                onclick="return confirm('Are you sure you want to delete this coupon?');"
                            >

                                🗑️ Delete

                            </a>


                        </td>


                    </tr>


                    <?php

                    $serial_no++;

                    ?>


                <?php endwhile; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="no-coupons">

                <h3>

                    No Coupons Found

                </h3>


                <p>

                    Click
                    <strong>
                        Add Coupon
                    </strong>
                    to create your first coupon.

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
