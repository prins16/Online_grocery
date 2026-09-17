<?php

session_start();

require_once "../../config/database.php";

/* Check admin login */

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit();
}

/* Get all categories */

$sql = "SELECT * FROM categories ORDER BY id ASC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Categories - Admin</title>

    <link rel="stylesheet"
          href="../../css/style.css">

    <style>

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header-box h1 {
            color: #1b5e20;
        }

        .add-btn {
            background: #2e7d32;
            color: white;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 5px;
        }

        .add-btn:hover {
            background: #1b5e20;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        th {
            background: #2e7d32;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
        }

        .edit-btn {
            background: #1976d2;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete-btn {
            background: #d32f2f;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .edit-btn:hover {
            background: #0d47a1;
        }

        .delete-btn:hover {
            background: #b71c1c;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background: #555;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
        }

        .empty {
            text-align: center;
            padding: 25px;
        }

    </style>

</head>

<body>

<header>

    <nav class="navbar">

        <div class="logo">
            Online Grocery - Admin
        </div>

        <ul class="nav-links">

            <li>
                <a href="../dashboard.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="../products/index.php">
                    Products
                </a>
            </li>

            <li>
                <a href="index.php">
                    Categories
                </a>
            </li>

        </ul>

    </nav>

</header>


<div class="container">

    <div class="header-box">

        <h1>
            📂 Manage Categories
        </h1>

        <a href="add.php" class="add-btn">
            + Add Category
        </a>

    </div>


    <table>

        <tr>

            <th>
                ID
            </th>

            <th>
                Category Name
            </th>

            <th>
                Description
            </th>

            <th>
                Status
            </th>

            <th>
                Actions
            </th>

        </tr>


        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php while ($category = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php
                        echo $category["id"];
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $category["name"]
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $category["description"]
                        );
                        ?>
                    </td>

                    <td>

                        <?php
                        echo htmlspecialchars(
                            $category["status"]
                        );
                        ?>

                    </td>

                    <td>

                        <a
                            href="edit.php?id=<?php echo $category["id"]; ?>"
                            class="edit-btn"
                        >
                            Edit
                        </a>

                        <a
                            href="delete.php?id=<?php echo $category["id"]; ?>"
                            class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this category?');"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>

                <td colspan="5" class="empty">

                    No categories found.

                </td>

            </tr>

        <?php endif; ?>

    </table>


    <a
        href="../dashboard.php"
        class="back-btn"
    >
        ← Back to Dashboard
    </a>

</div>


<footer>

    <p>
        &copy; 2026 Online Grocery Store.
        All Rights Reserved.
    </p>

</footer>

</body>

</html>
