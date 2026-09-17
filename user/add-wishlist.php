
<?php

session_start();

require_once "../config/database.php";

/* Check login */

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* Check product ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: ../products/index.php");
    exit();
}

$product_id = (int) $_GET["id"];

/* Check whether product exists */

$sql = "SELECT id FROM products WHERE id = ? AND status = 'active' LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    header("Location: ../products/index.php");
    exit();
}

mysqli_stmt_close($stmt);

/* Check whether already in wishlist */

$sql = "SELECT id
        FROM wishlist
        WHERE user_id = ? AND product_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $user_id,
    $product_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    /* Add to wishlist */

    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO wishlist (user_id, product_id)
            VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $user_id,
        $product_id
    );

    mysqli_stmt_execute($stmt);
}

mysqli_stmt_close($stmt);

/* Go to wishlist */

header("Location: wishlist.php");
exit();

?>
