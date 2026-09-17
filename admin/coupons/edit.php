
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

$error = "";


/* ==========================================
   UPDATE COUPON
========================================== */

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $code = strtoupper(
        trim($_POST["code"])
    );

    $discount_type =
        $_POST["discount_type"];

    $discount_value =
        trim($_POST["discount_value"]);

    $min_order =
        trim($_POST["min_order"]);

    $expiry_date =
        $_POST["expiry_date"];

    $status =
        $_POST["status"];


    /* ==========================================
       VALIDATION
    ========================================== */

    if (
        $code == "" ||
        $discount_value == "" ||
        $min_order == "" ||
        $expiry_date == ""
    ) {

        $error =
            "Please fill in all required fields.";

    }

    elseif (
        !in_array(
            $discount_type,
            ["percentage", "fixed"]
        )
    ) {

        $error =
            "Invalid discount type.";

    }

    elseif (
        !is_numeric($discount_value) ||
        $discount_value < 0
    ) {

        $error =
            "Please enter a valid discount value.";

    }

    elseif (
        !is_numeric($min_order) ||
        $min_order < 0
    ) {

        $error =
            "Please enter a valid minimum order amount.";

    }

    elseif (
        !in_array(
            $status,
            ["active", "inactive"]
        )
    ) {

        $error =
            "Invalid coupon status.";

    }

    elseif (
        $discount_type == "percentage" &&
        $discount_value > 100
    ) {

        $error =
            "Percentage discount cannot be more than 100%.";

    }


    /* ==========================================
       CHECK DUPLICATE CODE
    ========================================== */

    if ($error == "") {


        $check_stmt = mysqli_prepare(
            $conn,
            "SELECT id
             FROM coupons
             WHERE code = ?
             AND id != ?
             LIMIT 1"
        );


        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $code,
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
            ) > 0
        ) {

            $error =
                "This coupon code already exists.";

        }


        mysqli_stmt_close(
            $check_stmt
        );

    }


    /* ==========================================
       UPDATE DATABASE
    ========================================== */

    if ($error == "") {


        $stmt = mysqli_prepare(
            $conn,
            "UPDATE coupons
             SET
                code = ?,
                discount_type = ?,
                discount_value = ?,
                min_order = ?,
                expiry_date = ?,
                status = ?
             WHERE id = ?"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ssddssi",
            $code,
            $discount_type,
            $discount_value,
            $min_order,
            $expiry_date,
            $status,
            $coupon_id
        );


        if (
            mysqli_stmt_execute(
                $stmt
            )
        ) {

            header(
                "Location: index.php"
            );

            exit();

        }

        else {

            $error =
                "Unable to update coupon: "
                . mysqli_error($conn);

        }


        mysqli_stmt_close($stmt);

    }

}


/* ==========================================
   GET COUPON
========================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        code,
        discount_type,
        discount_value,
        min_order,
        expiry_date,
        status
     FROM coupons
     WHERE id = ?
     LIMIT 1"
);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $coupon_id
);


mysqli_stmt_execute($stmt);


$result =
    mysqli_stmt_get_result($stmt);


if (
    mysqli_num_rows($result) != 1
) {

    die("Coupon not found.");

}


$coupon =
    mysqli_fetch_assoc($result);


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
        Edit Coupon - Online Grocery
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


        input:focus,
        select:focus {

            outline: none;

            border-color: #2e7d32;

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


        .back-btn:hover {

            background: #444;

        }


        .error {

            background: #ffebee;

            color: #c62828;

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

            ✏️ Edit Coupon

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


        <form method="POST">


            <!-- COUPON CODE -->

            <div class="form-group">

                <label for="code">

                    Coupon Code

                </label>


                <input
                    type="text"
                    id="code"
                    name="code"
                    value="<?php

                    echo htmlspecialchars(
                        $coupon["code"]
                    );

                    ?>"
                    maxlength="50"
                    required
                >

            </div>


            <!-- DISCOUNT TYPE -->

            <div class="form-group">

                <label for="discount_type">

                    Discount Type

                </label>


                <select
                    id="discount_type"
                    name="discount_type"
                    required
                >

                    <option
                        value="percentage"
                        <?php

                        if (
                            $coupon["discount_type"]
                            == "percentage"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Percentage (%)

                    </option>


                    <option
                        value="fixed"
                        <?php

                        if (
                            $coupon["discount_type"]
                            == "fixed"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Fixed Amount (₹)

                    </option>


                </select>

            </div>


            <!-- DISCOUNT VALUE -->

            <div class="form-group">

                <label for="discount_value">

                    Discount Value

                </label>


                <input
                    type="number"
                    id="discount_value"
                    name="discount_value"
                    step="0.01"
                    min="0"
                    value="<?php

                    echo htmlspecialchars(
                        $coupon["discount_value"]
                    );

                    ?>"
                    required
                >

            </div>


            <!-- MINIMUM ORDER -->

            <div class="form-group">

                <label for="min_order">

                    Minimum Order Amount (₹)

                </label>


                <input
                    type="number"
                    id="min_order"
                    name="min_order"
                    step="0.01"
                    min="0"
                    value="<?php

                    echo htmlspecialchars(
                        $coupon["min_order"]
                    );

                    ?>"
                    required
                >

            </div>


            <!-- EXPIRY DATE -->

            <div class="form-group">

                <label for="expiry_date">

                    Expiry Date

                </label>


                <input
                    type="date"
                    id="expiry_date"
                    name="expiry_date"
                    value="<?php

                    echo htmlspecialchars(
                        $coupon["expiry_date"]
                    );

                    ?>"
                    required
                >

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label for="status">

                    Status

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
                            $coupon["status"]
                            == "active"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Active

                    </option>


                    <option
                        value="inactive"
                        <?php

                        if (
                            $coupon["status"]
                            == "inactive"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Inactive

                    </option>


                </select>

            </div>


            <!-- UPDATE BUTTON -->

            <button
                type="submit"
                class="update-btn"
            >

                💾 Update Coupon

            </button>


        </form>


        <a
            href="index.php"
            class="back-btn"
        >

            ← Back to Coupons

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