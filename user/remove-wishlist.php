
<?php

session_start();

require_once "../config/database.php";

/* Check login */

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* Check wishlist ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: wishlist.php");
    exit();
}

$wishlist_id = (int) $_GET["id"];

/* Delete only user's own wishlist item */

$sql = "DELETE FROM wishlist
        WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $wishlist_id,
    $user_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

/* Return to wishlist */

header("Location: wishlist.php");
exit();

?>
