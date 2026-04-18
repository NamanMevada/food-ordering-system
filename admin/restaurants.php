<?php
include '../includes/auth.php';
checkRole('admin');
include '../includes/db.php';

// Approve or Reject action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id     = $_GET['id'];
    if ($action == 'approve') {
        mysqli_query($conn, "UPDATE restaurants SET status='approved' WHERE id=$id");
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE restaurants SET status='rejected' WHERE id=$id");
    }
    header("Location: restaurants.php");
    exit();
}

// Get all restaurants
$restaurants = mysqli_query(
    $conn,
    "SELECT r.*, u.name as owner_name, u.email as owner_email 
     FROM restaurants r 
     JOIN users u ON r.user_id = u.id 
     ORDER BY r.created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Restaurants - Admin</title>
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
                <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;">Manage Restaurants</h4>
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Restaurant</th>
                            <th>Owner</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($r = mysqli_fetch_assoc($restaurants)): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $r['name'] ?><br>
                                    <small class="text-muted"><?= $r['address'] ?></small>
                                </td>
                                <td><?= $r['owner_name'] ?><br>
                                    <small class="text-muted"><?= $r['owner_email'] ?></small>
                                </td>
                                <td><?= $r['phone'] ?></td>
                                <td>
                                    <?php if ($r['status'] == 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif ($r['status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($r['status'] == 'pending'): ?>
                                        <a href="?action=approve&id=<?= $r['id'] ?>"
                                            class="btn btn-sm btn-success">Approve</a>
                                        <a href="?action=reject&id=<?= $r['id'] ?>"
                                            class="btn btn-sm btn-danger"> Reject</a>
                                    <?php elseif ($r['status'] == 'approved'): ?>
                                        <a href="?action=reject&id=<?= $r['id'] ?>"
                                            class="btn btn-sm btn-danger"> Reject</a>
                                    <?php else: ?>
                                        <a href="?action=approve&id=<?= $r['id'] ?>"
                                            class="btn btn-sm btn-success"> Approve</a>
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