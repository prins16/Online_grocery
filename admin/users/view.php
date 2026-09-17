
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
   CHECK USER ID
========================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    die("Invalid user ID.");

}

$user_id = (int) $_GET["id"];


/* ==========================================
   GET USER
========================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        fullname,
        email,
        phone,
        created_at,
        updated_at,
        status
     FROM users
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) != 1) {

    die("User not found.");

}

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

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
        View User - Online Grocery
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

            width: 90%;

            max-width: 700px;

            margin: 40px auto;

        }

        .box {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }

        h1 {

            color: #1b5e20;

            margin-bottom: 25px;

        }

        .info {

            padding: 15px 0;

            border-bottom: 1px solid #eee;

        }

        .info strong {

            display: inline-block;

            width: 150px;

            color: #555;

        }

        .status-active {

            color: #2e7d32;

            font-weight: bold;

        }

        .status-blocked {

            color: #c62828;

            font-weight: bold;

        }

        .btn {

            display: inline-block;

            margin-top: 25px;

            margin-right: 8px;

            padding: 10px 18px;

            border-radius: 5px;

            text-decoration: none;

            color: white;

        }

        .back {

            background: #2e7d32;

        }

        .edit {

            background: #f9a825;

        }

        .back:hover {

            background: #1b5e20;

        }

        .edit:hover {

            background: #f57f17;

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


        <h1>

            👤 User Details

        </h1>


        <!-- USER ID -->

        <div class="info">

            <strong>
                User ID:
            </strong>

            <?php

            echo htmlspecialchars(
                $user["id"]
            );

            ?>

        </div>


        <!-- FULL NAME -->

        <div class="info">

            <strong>
                Full Name:
            </strong>

            <?php

            echo htmlspecialchars(
                $user["fullname"]
            );

            ?>

        </div>


        <!-- EMAIL -->

        <div class="info">

            <strong>
                Email:
            </strong>

            <?php

            echo htmlspecialchars(
                $user["email"]
            );

            ?>

        </div>


        <!-- PHONE -->

        <div class="info">

            <strong>
                Phone:
            </strong>

            <?php

            echo htmlspecialchars(
                $user["phone"]
            );

            ?>

        </div>


        <!-- STATUS -->

        <div class="info">

            <strong>
                Status:
            </strong>


            <?php if (
                $user["status"] == "active"
            ): ?>

                <span class="status-active">

                    Active

                </span>

            <?php else: ?>

                <span class="status-blocked">

                    Blocked

                </span>

            <?php endif; ?>


        </div>


        <!-- CREATED -->

        <div class="info">

            <strong>
                Created At:
            </strong>

            <?php

            echo htmlspecialchars(
                $user["created_at"]
            );

            ?>

        </div>


        <!-- UPDATED -->

        <div class="info">

            <strong>
                Updated At:
            </strong>

            <?php

            echo htmlspecialchars(
                $user["updated_at"] ?? "Not updated"
            );

            ?>

        </div>


        <!-- BUTTONS -->

        <a
            href="index.php"
            class="btn back"
        >

            ← Back to Users

        </a>


        <a
            href="edit.php?id=<?php echo $user["id"]; ?>"
            class="btn edit"
        >

            ✏️ Edit User

        </a>


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
?>