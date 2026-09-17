
<?php

session_start();

require_once "../../config/database.php";

/* Check admin login */

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

/* Check category ID */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$category_id = (int) $_GET["id"];


/* Check whether category exists */

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM categories WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param(
    $check,
    "i",
    $category_id
);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($check);

    header("Location: index.php");
    exit();
}

mysqli_stmt_close($check);


/*
 * Check whether products are using
 * this category.
 */

$product_check = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM products
     WHERE category_id = ?"
);

mysqli_stmt_bind_param(
    $product_check,
    "i",
    $category_id
);

mysqli_stmt_execute($product_check);

$product_result =
    mysqli_stmt_get_result($product_check);

$product_data =
    mysqli_fetch_assoc($product_result);

mysqli_stmt_close($product_check);


/*
 * Do not delete a category if
 * products are connected to it.
 */

if ($product_data["total"] > 0) {

    echo "<script>
            alert('This category cannot be deleted because products are using it.');
            window.location.href = 'index.php';
          </script>";

    exit();
}


/* Delete category */

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM categories
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $delete,
    "i",
    $category_id
);

mysqli_stmt_execute($delete);

mysqli_stmt_close($delete);


/* Return to category list */

header("Location: index.php");
exit();

?>
