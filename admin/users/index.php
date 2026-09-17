
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
   GET USERS
========================================== */

$sql = "SELECT
            id,
            fullname,
            email,
            phone,
            created_at,
            status
        FROM users
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Database Error: "
        . mysqli_error($conn)
    );

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
        Manage Users - Online Grocery
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


        .back-btn {

            display: inline-block;

            margin-top: 15px;

            padding: 10px 18px;

            background: #2e7d32;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .back-btn:hover {

            background: #1b5e20;

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

            padding: 13px;

            text-align: left;

        }


        td {

            padding: 12px;

            border-bottom: 1px solid #ddd;

            color: #333;

        }


        tr:hover {

            background: #f1f8f2;

        }


        .user-id {

            font-weight: bold;

            color: #1b5e20;

        }


        .status-active {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 15px;

            background: #e8f5e9;

            color: #2e7d32;

            font-weight: bold;

        }


        .status-blocked {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 15px;

            background: #ffebee;

            color: #c62828;

            font-weight: bold;

        }


        .action-btn {

            display: inline-block;

            padding: 7px 12px;

            margin: 2px;

            border-radius: 5px;

            text-decoration: none;

            font-size: 14px;

        }


        .view-btn {

            background: #1976d2;

            color: white;

        }


        .view-btn:hover {

            background: #0d47a1;

        }


        .edit-btn {

            background: #f9a825;

            color: white;

        }


        .edit-btn:hover {

            background: #f57f17;

        }


        .delete-btn {

            background: #c62828;

            color: white;

        }


        .delete-btn:hover {

            background: #8e0000;

        }


        .no-users {

            text-align: center;

            padding: 30px;

            color: #777;

        }


        @media (max-width: 700px) {

            .admin-container {

                width: 95%;

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


    <!-- ==========================================
         PAGE HEADER
    ========================================== -->

    <div class="page-header">

        <h1>

            👥 Manage Users

        </h1>


        <p>

            View and manage registered customers.

        </p>


        <a
            href="../dashboard.php"
            class="back-btn"
        >

            ← Back to Dashboard

        </a>

    </div>


    <!-- ==========================================
         USERS TABLE
    ========================================== -->

    <div class="table-box">


        <?php if (mysqli_num_rows($result) > 0): ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Full Name
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
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
                        $user = mysqli_fetch_assoc($result)
                    ): ?>


                        <tr>


                            <!-- ID -->

                            <td class="user-id">

                              <?php

static $display_id = 1;

echo $display_id++;

?>
                            </td>


                            <!-- NAME -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $user["fullname"]
                                );

                                ?>

                            </td>


                            <!-- EMAIL -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $user["email"]
                                );

                                ?>

                            </td>


                            <!-- PHONE -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $user["phone"]
                                );

                                ?>

                            </td>


                            <!-- STATUS -->

                            <td>


                                <?php if (
                                    $user["status"] == "active"
                                ): ?>


                                    <span
                                        class="status-active"
                                    >

                                        Active

                                    </span>


                                <?php else: ?>


                                    <span
                                        class="status-blocked"
                                    >

                                        Blocked

                                    </span>


                                <?php endif; ?>


                            </td>


                            <!-- CREATED AT -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $user["created_at"]
                                );

                                ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>


                                <!-- VIEW -->

                                <a
                                    href="view.php?id=<?php echo $user["id"]; ?>"
                                    class="action-btn view-btn"
                                >

                                    👁️ View

                                </a>


                                <!-- EDIT -->

                                <a
                                    href="edit.php?id=<?php echo $user["id"]; ?>"
                                    class="action-btn edit-btn"
                                >

                                    ✏️ Edit

                                </a>


                                <!-- DELETE -->

                                <a
                                    href="delete.php?id=<?php echo $user["id"]; ?>"
                                    class="action-btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this user?');"
                                >

                                    🗑️ Delete

                                </a>


                            </td>


                        </tr>


                    <?php endwhile; ?>


                </tbody>


            </table>


        <?php else: ?>


            <div class="no-users">

                <h3>

                    No Users Found

                </h3>


                <p>

                    There are currently no registered users.

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