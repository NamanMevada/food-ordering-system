<?php
include '../includes/auth.php';
checkRole('admin');
include '../includes/db.php';



if(isset($_GET['action']) && isset($_GET['id'])){
    $action = $_GET['action'];
    $id     = $_GET['id'];
    if($action == 'block'){
        mysqli_query($conn, "UPDATE users SET status='inactive' WHERE id=$id");
    } elseif($action == 'unblock'){
        mysqli_query($conn, "UPDATE users SET status='active' WHERE id=$id");
    }
    header("Location: users.php");
    exit();
}



$users = mysqli_query($conn, 
    "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users - Admin</title>
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
        .sidebar a:hover { background: #e74c3c; }
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
            <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;">Manage Users</h4>
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php $i=1; while($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $u['name'] ?></td>
                    <td><?= $u['email'] ?></td>
                    <td>
                        <?php if($u['role'] == 'restaurant'): ?>
                            <span class="badge bg-primary">Restaurant</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Customer</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($u['status'] == 'active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Blocked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($u['status'] == 'active'): ?>
                            <a href="?action=block&id=<?= $u['id'] ?>" 
                               class="btn btn-sm btn-danger">Block</a>
                        <?php else: ?>
                            <a href="?action=unblock&id=<?= $u['id'] ?>" 
                               class="btn btn-sm btn-success">Unblock</a>
                        <?php endif; ?>
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