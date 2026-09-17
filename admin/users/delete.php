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

    header("Location: index.php");
    exit();

}

$user_id = (int) $_GET["id"];


/* ==========================================
   START TRANSACTION
========================================== */

mysqli_begin_transaction($conn);

try {


    /* ==========================================
       DELETE CART ITEMS
    ========================================== */

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM cart
         WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* ==========================================
       DELETE WISHLIST ITEMS
    ========================================== */

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM wishlist
         WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* ==========================================
       DELETE PRODUCT REVIEWS
    ========================================== */

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM product_reviews
         WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* ==========================================
       PROTECT ORDER HISTORY
       
       Addresses used by orders are kept,
       but user_id is changed to NULL.
    ========================================== */

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE addresses a
         INNER JOIN orders o
         ON a.id = o.address_id
         SET a.user_id = NULL
         WHERE a.user_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* ==========================================
       DELETE UNUSED ADDRESSES
    ========================================== */

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM addresses
         WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* ==========================================
       DELETE USER
    ========================================== */

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM users
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /* ==========================================
       COMMIT
    ========================================== */

    mysqli_commit($conn);


    /* ==========================================
       RETURN TO USERS PAGE
    ========================================== */

    header("Location: index.php");

    exit();


} catch (Exception $e) {


    /* ==========================================
       ROLLBACK
    ========================================== */

    mysqli_rollback($conn);


    echo "Unable to delete user.";

    echo "<br><br>";

    echo "Error: "
        . htmlspecialchars(
            $e->getMessage()
        );

    echo "<br><br>";

    echo '<a href="index.php">
            ← Back to Manage Users
          </a>';

}

?>