
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
   CHECK PRODUCT ID
========================================== */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: index.php");
    exit();

}

$product_id = (int) $_GET["id"];


/* ==========================================
   CHECK PRODUCT EXISTS
========================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, name
     FROM products
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);

    header("Location: index.php");
    exit();

}

$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* ==========================================
   DELETE PRODUCT
========================================== */

$delete_stmt = mysqli_prepare(
    $conn,
    "DELETE FROM products
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $delete_stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($delete_stmt);

mysqli_stmt_close($delete_stmt);


/* ==========================================
   RETURN TO PRODUCTS
========================================== */

header("Location: index.php");
exit();

?>
