
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

$error = "";

/* Get category */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, name, description
     FROM categories
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $category_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);

    echo "Category not found.";
    exit();
}

$category = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* Update category */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);

    if ($name == "") {

        $error = "Please enter category name.";

    } else {

        /* Check duplicate category */

        $check = mysqli_prepare(
            $conn,
            "SELECT id
             FROM categories
             WHERE name = ?
             AND id != ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $check,
            "si",
            $name,
            $category_id
        );

        mysqli_stmt_execute($check);

        $check_result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "Another category with this name already exists.";

        } else {

            /* Update */

            $update = mysqli_prepare(
                $conn,
                "UPDATE categories
                 SET name = ?, description = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "ssi",
                $name,
                $description,
                $category_id
            );

            if (mysqli_stmt_execute($update)) {

                mysqli_stmt_close($update);
                mysqli_stmt_close($check);

                header("Location: index.php");
                exit();

            } else {

                $error = "Failed to update category.";
            }

            mysqli_stmt_close($update);
        }

        mysqli_stmt_close($check);
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
        Edit Category - Admin
    </title>

    <link
        rel="stylesheet"
        href="../../css/style.css"
    >

    <style>

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #1b5e20;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 16px;
        }

        .save-btn {
            background: #2e7d32;
            color: white;
        }

        .save-btn:hover {
            background: #1b5e20;
        }

        .back-btn {
            background: #777;
            color: white;
            margin-left: 10px;
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
            ✏️ Edit Category
        </h1>


        <?php if ($error != ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="name">
                    Category Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($category["name"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                ><?php echo htmlspecialchars($category["description"]); ?></textarea>

            </div>


            <button
                type="submit"
                class="btn save-btn"
            >
                💾 Update Category
            </button>


            <a
                href="index.php"
                class="btn back-btn"
            >
                ← Back
            </a>

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