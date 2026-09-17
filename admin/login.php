
<?php

session_start();

require_once "../config/database.php";

$error = "";

/* ==========================================
   ADMIN LOGIN
========================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = isset($_POST["email"])
        ? trim($_POST["email"])
        : "";

    $password = isset($_POST["password"])
        ? trim($_POST["password"])
        : "";

    /* Check empty fields */

    if ($email === "" || $password === "") {

        $error = "Please enter email and password.";

    } else {

        /* Find admin by email */

        $sql = "SELECT id, name, email, password, status
                FROM admin
                WHERE email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {

            $error = "Database error: " . mysqli_error($conn);

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $email
            );

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {

                $admin = mysqli_fetch_assoc($result);

                /* Check account status */

                if ($admin["status"] !== "active") {

                    $error = "Your admin account is inactive.";

                }

                /* Check password */

                elseif ($password === $admin["password"]) {

                    /* Save admin information in session */

                    $_SESSION["admin_id"] =
                        $admin["id"];

                    $_SESSION["admin_name"] =
                        $admin["name"];

                    $_SESSION["admin_email"] =
                        $admin["email"];

                    /* Go to admin dashboard */

                    header(
                        "Location: dashboard.php"
                    );

                    exit();

                } else {

                    $error =
                        "Invalid email or password.";
                }

            } else {

                $error =
                    "Invalid email or password.";
            }

            mysqli_stmt_close($stmt);
        }
    }
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
        Admin Login - Online Grocery
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

    <style>

        .admin-login-container {

            width: 90%;

            max-width: 450px;

            margin: 80px auto;

        }

        .admin-login-box {

            background: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 3px 15px
                rgba(0, 0, 0, 0.1);

        }

        .admin-login-box h1 {

            text-align: center;

            color: #1b5e20;

            margin-bottom: 25px;

        }

        .form-group {

            margin-bottom: 18px;

        }

        .form-group label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

        }

        .form-group input {

            width: 100%;

            padding: 12px;

            box-sizing: border-box;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-size: 16px;

        }

        .admin-login-btn {

            width: 100%;

            padding: 13px;

            background: #2e7d32;

            color: white;

            border: none;

            border-radius: 5px;

            font-size: 16px;

            cursor: pointer;

        }

        .admin-login-btn:hover {

            background: #1b5e20;

        }

        .error {

            background: #ffebee;

            color: #c62828;

            padding: 12px;

            border-radius: 5px;

            margin-bottom: 20px;

            text-align: center;

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


<div class="admin-login-container">

    <div class="admin-login-box">

        <h1>
            👨‍💼 Admin Login
        </h1>


        <?php if ($error !== ""): ?>

            <div class="error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter admin email"
                    value="<?php
                        echo isset($_POST["email"])
                            ? htmlspecialchars($_POST["email"])
                            : "";
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter admin password"
                    required
                >

            </div>


            <button
                type="submit"
                class="admin-login-btn"
            >

                Login as Admin

            </button>

        </form>

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