<?php
include '../includes/auth.php';
checkRole('restaurant');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$check = mysqli_query(
    $conn,
    "SELECT * FROM restaurants WHERE user_id=$user_id"
);
$restaurant = mysqli_fetch_assoc($check);

if (!$restaurant || $restaurant['status'] != 'approved') {
    header("Location: dashboard.php");
    exit();
}

$rest_id = $restaurant['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id  = $_POST['order_id'];
    $status    = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query(
        $conn,
        "UPDATE orders SET status='$status' WHERE id=$order_id 
         AND restaurant_id=$rest_id"
    );
    header("Location: orders.php");
    exit();
}

$orders = mysqli_query(
    $conn,
    "SELECT o.*, u.name AS customer_name, u.email AS customer_email
     FROM orders o
     JOIN users u ON o.customer_id = u.id
     WHERE o.restaurant_id = $rest_id
     ORDER BY o.created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

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

<body>
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-2 sidebar">
                <div class="brand"> 
                    Food<span>Hub</span>
                </div>
                <a href="dashboard.php"><i class="ri-dashboard-fill"></i>Dashboard</a>
                <a href="menu.php"><i class="ri-menu-line"></i>Menu Items</a>
                <a href="orders.php"><i class="ri-shopping-bag-3-fill"></i>Orders</a>
                <a href="../logout.php"><i class="ri-logout-box-line"></i>Logout</a>
            </div>


            <div class="col-md-10 p-4">
                <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;">Manage Orders</h4>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Update Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                while ($order = mysqli_fetch_assoc($orders)): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $order['customer_name'] ?><br>
                                            <small class="text-muted">
                                                <?= $order['customer_email'] ?>
                                            </small>
                                        </td>
                                        <td>₹<?= $order['total_amount'] ?></td>
                                        <td><?= $order['address'] ?></td>
                                        <td>
                                            <?php
                                            $status = $order['status'];
                                            if ($status == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif ($status == 'confirmed'): ?>
                                                <span class="badge bg-primary">Confirmed</span>
                                            <?php elseif ($status == 'preparing'): ?>
                                                <span class="badge bg-info">Preparing</span>
                                            <?php elseif ($status == 'delivered'): ?>
                                                <span class="badge bg-success">Delivered</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>

                                            <form method="POST">
                                                <input type="hidden"
                                                    name="order_id"
                                                    value="<?= $order['id'] ?>">
                                                <div class="input-group">
                                                    <select name="status" class="form-select form-select-sm">
                                                        <option value="pending"
                                                            <?= $status == 'pending' ? 'selected' : '' ?>>
                                                            Pending
                                                        </option>
                                                        <option value="confirmed"
                                                            <?= $status == 'confirmed' ? 'selected' : '' ?>>
                                                            Confirmed
                                                        </option>
                                                        <option value="preparing"
                                                            <?= $status == 'preparing' ? 'selected' : '' ?>>
                                                            Preparing
                                                        </option>
                                                        <option value="delivered"
                                                            <?= $status == 'delivered' ? 'selected' : '' ?>>
                                                            Delivered
                                                        </option>
                                                        <option value="cancelled"
                                                            <?= $status == 'cancelled' ? 'selected' : '' ?>>
                                                            Cancelled
                                                        </option>
                                                    </select>
                                                    <button type="submit"
                                                        class="btn btn-sm btn-danger">
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>