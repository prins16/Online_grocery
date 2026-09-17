
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
   CHECK COUPON ID
========================================== */

if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {

    die("Invalid coupon ID.");

}

$coupon_id = (int) $_GET["id"];


/* ==========================================
   CHECK COUPON EXISTS
========================================== */

$check_stmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM coupons
     WHERE id = ?
     LIMIT 1"
);


mysqli_stmt_bind_param(
    $check_stmt,
    "i",
    $coupon_id
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
    ) != 1
) {

    mysqli_stmt_close(
        $check_stmt
    );

    die("Coupon not found.");

}


mysqli_stmt_close(
    $check_stmt
);


/* ==========================================
   DELETE COUPON
========================================== */

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM coupons
     WHERE id = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $coupon_id
);


if (
    mysqli_stmt_execute($stmt)
) {

    mysqli_stmt_close($stmt);

    header(
        "Location: index.php"
    );

    exit();

}

else {

    $error =
        "Unable to delete coupon: "
        . mysqli_error($conn);

    mysqli_stmt_close($stmt);

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
        Delete Coupon - Online Grocery
    </title>

    <style>

        body {

            font-family: Arial, sans-serif;

            background: #f5f7f5;

        }

        .box {

            width: 90%;

            max-width: 600px;

            margin: 100px auto;

            background: white;

            padding: 30px;

            border-radius: 10px;

            text-align: center;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.1);

        }

        h2 {

            color: #c62828;

        }

        .error {

            color: #c62828;

            margin-bottom: 20px;

        }

        a {

            display: inline-block;

            padding: 10px 20px;

            background: #2e7d32;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }

    </style>

</head>

<body>


<div class="box">

    <h2>

        Unable to Delete Coupon

    </h2>


    <p class="error">

        <?php

        echo htmlspecialchars(
            $error
        );

        ?>

    </p>


    <a href="index.php">

        ← Back to Coupons

    </a>

</div>


</body>

</html>
?>