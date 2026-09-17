
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

$error = "";

$success = "";


/* ==========================================
   UPDATE USER
========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $fullname = trim(
        $_POST["fullname"]
    );


    $email = trim(
        $_POST["email"]
    );


    $phone = trim(
        $_POST["phone"]
    );


    $status = $_POST["status"];


    if (
        $fullname == "" ||
        $email == ""
    ) {

        $error =
            "Full name and email are required.";

    }

    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid email address.";

    }

    elseif (
        !in_array(
            $status,
            ["active", "blocked"]
        )
    ) {

        $error =
            "Invalid account status.";

    }

    else {


        /* ==========================================
           CHECK EMAIL
        ========================================== */

        $check_stmt = mysqli_prepare(
            $conn,
            "SELECT id
             FROM users
             WHERE email = ?
             AND id != ?
             LIMIT 1"
        );


        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $email,
            $user_id
        );


        mysqli_stmt_execute(
            $check_stmt
        );


        $check_result =
            mysqli_stmt_get_result(
                $check_stmt
            );


        if (
            mysqli_num_rows(
                $check_result
            ) > 0
        ) {

            $error =
                "This email is already used by another user.";

        }


        mysqli_stmt_close(
            $check_stmt
        );


        /* ==========================================
           UPDATE
        ========================================== */

        if ($error == "") {


            $stmt = mysqli_prepare(
                $conn,
                "UPDATE users
                 SET
                    fullname = ?,
                    email = ?,
                    phone = ?,
                    status = ?,
                    updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?"
            );


            mysqli_stmt_bind_param(
                $stmt,
                "ssssi",
                $fullname,
                $email,
                $phone,
                $status,
                $user_id
            );


            if (
                mysqli_stmt_execute(
                    $stmt
                )
            ) {

                $success =
                    "User updated successfully.";

            }

            else {

                $error =
                    "Unable to update user: "
                    . mysqli_error($conn);

            }


            mysqli_stmt_close(
                $stmt
            );

        }

    }

}


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


mysqli_stmt_execute(
    $stmt
);


$result =
    mysqli_stmt_get_result(
        $stmt
    );


if (
    mysqli_num_rows(
        $result
    ) != 1
) {

    die("User not found.");

}


$user =
    mysqli_fetch_assoc(
        $result
    );


mysqli_stmt_close(
    $stmt
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
        Edit User - Online Grocery
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

            max-width: 650px;

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


        .form-group {

            margin-bottom: 18px;

        }


        label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

        }


        input,
        select {

            width: 100%;

            padding: 12px;

            box-sizing: border-box;

            border: 1px solid #ccc;

            border-radius: 5px;

        }


        .update-btn {

            width: 100%;

            padding: 13px;

            background: #2e7d32;

            color: white;

            border: none;

            border-radius: 5px;

            font-size: 16px;

            cursor: pointer;

        }


        .update-btn:hover {

            background: #1b5e20;

        }


        .back-btn {

            display: inline-block;

            margin-top: 15px;

            padding: 10px 18px;

            background: #666;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .error {

            background: #ffebee;

            color: #c62828;

            padding: 12px;

            border-radius: 5px;

            margin-bottom: 20px;

        }


        .success {

            background: #e8f5e9;

            color: #2e7d32;

            padding: 12px;

            border-radius: 5px;

            margin-bottom: 20px;

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

            ✏️ Edit User

        </h1>


        <?php if ($error != ""): ?>

            <div class="error">

                <?php

                echo htmlspecialchars(
                    $error
                );

                ?>

            </div>

        <?php endif; ?>


        <?php if ($success != ""): ?>

            <div class="success">

                <?php

                echo htmlspecialchars(
                    $success
                );

                ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- FULL NAME -->

            <div class="form-group">

                <label for="fullname">

                    Full Name

                </label>


                <input
                    type="text"
                    id="fullname"
                    name="fullname"
                    value="<?php echo htmlspecialchars($user["fullname"]); ?>"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">

                    Email

                </label>


                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    required
                >

            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label for="phone">

                    Phone

                </label>


                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($user["phone"]); ?>"
                >

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label for="status">

                    Account Status

                </label>


                <select
                    id="status"
                    name="status"
                    required
                >


                    <option
                        value="active"
                        <?php

                        if (
                            $user["status"]
                            == "active"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Active

                    </option>


                    <option
                        value="blocked"
                        <?php

                        if (
                            $user["status"]
                            == "blocked"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Blocked

                    </option>


                </select>

            </div>


            <!-- UPDATE -->

            <button
                type="submit"
                class="update-btn"
            >

                💾 Update User

            </button>


        </form>


        <a
            href="index.php"
            class="back-btn"
        >

            ← Back to Users

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