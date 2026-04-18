<?php

include '../includes/auth.php';
checkRole('admin');
include '../includes/db.php';

$success = "";
$error   = "";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header("Location: categories.php");
    exit();
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            padding: 20px 0;
        }

        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: flex;
            padding: 12px 20px;
            transition: 0.3s;
            gap: 12px;
        }

        .sidebar a i {
            font-size: 20px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #e74c3c;
        }

        .sidebar .brand {
            color: white;
            font-size: 22px;
            font-weight: 800;
            padding: 10px 20px 25px;
            border-bottom: 1px solid #455a64;
            margin-bottom: 10px;
        }
        .sidebar .brand span {
            color: #ff4757;
        }
    </style>

</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="brand">
                    Food<span>Hub</span>
                </div>
                <a href="dashboard.php"><i class="ri-dashboard-fill"></i>Dashboard</a>
                <a href="restaurants.php"><i class="ri-restaurant-2-fill"></i>Restaurant</a>
                <a href="users.php"><i class="ri-group-fill"></i>users</a>
                <a href="categories.php"><i class="ri-menu-line"></i>Categories</a>
                <a href="../logout.php"><i class="ri-logout-box-line"></i>Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;"> Manage Categories</h4>

                <div class="card mb-4" style="max-width:400px;">
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="input-group">
                                <input type="text" name="name"
                                    class="form-control"
                                    placeholder="New category name" required>
                                <button type="submit" name="add_category"
                                    class="btn btn-danger">Add</button>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered bg-white" style="max-width:400px;">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($c = mysqli_fetch_assoc($categories)): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $c['name'] ?></td>
                                <td>
                                    <a href="?delete=<?= $c['id'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this category?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</body>

</html>