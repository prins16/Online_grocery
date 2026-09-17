
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
   UPDATE REVIEW STATUS
========================================== */

if (
    isset($_GET["action"]) &&
    isset($_GET["id"]) &&
    is_numeric($_GET["id"])
) {

    $action = $_GET["action"];
    $review_id = (int) $_GET["id"];


    if (
        in_array(
            $action,
            ["approved", "pending", "rejected"]
        )
    ) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE product_reviews
             SET status = ?
             WHERE id = ?"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $action,
            $review_id
        );


        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);


        header(
            "Location: index.php"
        );

        exit();

    }

}


/* ==========================================
   DELETE REVIEW
========================================== */

if (
    isset($_GET["delete"]) &&
    is_numeric($_GET["delete"])
) {

    $review_id =
        (int) $_GET["delete"];


    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM product_reviews
         WHERE id = ?"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $review_id
    );


    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    header(
        "Location: index.php"
    );

    exit();

}


/* ==========================================
   GET REVIEWS
========================================== */

$query = "

    SELECT

        pr.id,

        pr.rating,

        pr.review,

        pr.status,

        pr.created_at,

        u.fullname AS user_name,

        p.name AS product_name

    FROM product_reviews pr

    LEFT JOIN users u
        ON pr.user_id = u.id

    LEFT JOIN products p
        ON pr.product_id = p.id

    ORDER BY pr.created_at DESC

";


$result = mysqli_query(
    $conn,
    $query
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
        Product Reviews - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        body {

            background: #f5f7f5;

        }


        .container {

            width: 95%;

            max-width: 1200px;

            margin: 40px auto;

        }


        .header-box {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

            margin-bottom: 25px;

        }


        h1 {

            color: #1b5e20;

            margin: 0 0 10px 0;

        }


        .back-btn {

            display: inline-block;

            padding: 10px 18px;

            background: #666;

            color: white;

            text-decoration: none;

            border-radius: 5px;

            margin-top: 10px;

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

            min-width: 900px;

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

            vertical-align: top;

        }


        tr:hover {

            background: #f5f5f5;

        }


        .rating {

            color: #f9a825;

            font-size: 18px;

            white-space: nowrap;

        }


        .status {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: bold;

        }


        .approved {

            background: #e8f5e9;

            color: #2e7d32;

        }


        .pending {

            background: #fff8e1;

            color: #f57f17;

        }


        .rejected {

            background: #ffebee;

            color: #c62828;

        }


        .actions {

            display: flex;

            flex-wrap: wrap;

            gap: 5px;

        }


        .action-btn {

            display: inline-block;

            padding: 6px 9px;

            color: white;

            text-decoration: none;

            border-radius: 4px;

            font-size: 12px;

        }


        .approve {

            background: #2e7d32;

        }


        .pending-btn {

            background: #f9a825;

        }


        .reject {

            background: #c62828;

        }


        .delete {

            background: #424242;

        }


        .no-reviews {

            text-align: center;

            padding: 30px;

            color: #777;

        }


        @media (max-width: 700px) {

            .container {

                width: 92%;

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


    <!-- HEADER -->

    <div class="header-box">

        <h1>

            ⭐ Product Reviews

        </h1>

        <p>

            Manage customer product reviews
            and their approval status.

        </p>


        <a
            href="../dashboard.php"
            class="back-btn"
        >

            ← Back to Dashboard

        </a>

    </div>


    <!-- REVIEWS TABLE -->

    <div class="table-box">


        <?php if (
            mysqli_num_rows($result) > 0
        ): ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            User
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Rating
                        </th>

                        <th>
                            Review
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php while (
                    $row =
                    mysqli_fetch_assoc($result)
                ): ?>


                    <tr>


                        <td>

                            <?php

                            echo (int)
                                $row["id"];

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["user_name"]
                                ?? "Unknown User"
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["product_name"]
                                ?? "Unknown Product"
                            );

                            ?>

                        </td>


                        <td>

                            <span class="rating">

                                <?php

                                $rating =
                                    (int)
                                    $row["rating"];

                                for (
                                    $i = 1;
                                    $i <= 5;
                                    $i++
                                ) {

                                    echo
                                        $i <= $rating
                                        ? "★"
                                        : "☆";

                                }

                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["review"]
                                ?? ""
                            );

                            ?>

                        </td>


                        <td>

                            <span
                                class="status
                                <?php

                                echo htmlspecialchars(
                                    $row["status"]
                                );

                                ?>"
                            >

                                <?php

                                echo ucfirst(
                                    htmlspecialchars(
                                        $row["status"]
                                    )
                                );

                                ?>

                            </span>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["created_at"]
                            );

                            ?>

                        </td>


                        <td>


                            <div class="actions">


                                <a
                                    href="index.php?action=approved&id=<?php
                                    echo (int)
                                        $row["id"];
                                    ?>"
                                    class="action-btn approve"
                                    onclick="return confirm('Approve this review?');"
                                >

                                    ✓ Approve

                                </a>


                                <a
                                    href="index.php?action=pending&id=<?php
                                    echo (int)
                                        $row["id"];
                                    ?>"
                                    class="action-btn pending-btn"
                                    onclick="return confirm('Set this review as pending?');"
                                >

                                    ⏳ Pending

                                </a>


                                <a
                                    href="index.php?action=rejected&id=<?php
                                    echo (int)
                                        $row["id"];
                                    ?>"
                                    class="action-btn reject"
                                    onclick="return confirm('Reject this review?');"
                                >

                                    ✕ Reject

                                </a>


                                <a
                                    href="index.php?delete=<?php
                                    echo (int)
                                        $row["id"];
                                    ?>"
                                    class="action-btn delete"
                                    onclick="return confirm('Are you sure you want to permanently delete this review?');"
                                >

                                    🗑 Delete

                                </a>


                            </div>


                        </td>


                    </tr>


                <?php endwhile; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="no-reviews">

                <h3>

                    No product reviews found.

                </h3>

                <p>

                    Customer reviews will appear
                    here when they are submitted.

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
