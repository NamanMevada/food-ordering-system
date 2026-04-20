<?php
include '../includes/auth.php';
checkRole('customer');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Saare orders fetch karo
$orders = mysqli_query($conn,
    "SELECT o.*, r.name AS restaurant_name
     FROM orders o
     JOIN restaurants r ON o.restaurant_id = r.id
     WHERE o.customer_id = $user_id
     ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar {
    padding: 14px 30px;
}
        .navbar-brand {
            color: #e74c3c !important;
            font-weight: 800;
            font-size: 24px;
        }
        .order-card {
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        .order-header {
            background: #f8f9fa;
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .price {
            color: #e74c3c;
            font-weight: 800;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar shadow-sm px-4" style="background:#2c3e50;">
        <a class="navbar-brand">
    <span style="color:white;">Food</span><span style="color:#ff4757;">Hub</span>
</a>
        <div>
            <a href="home.php" class="btn btn-outline-light me-2">
                <i class="ri-home-4-line me-1"></i>
                 Home
            </a>
            <a href="cart.php" class="btn btn-outline-light me-2">
                <i class="ri-shopping-cart-2-line me-1"></i>
                 Cart
            </a>
            <a href="../logout.php" class="btn btn-danger">
                <i class="ri-logout-box-r-line me-1"></i>
                Logout
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;"> My Orders</h4>

        <?php if(mysqli_num_rows($orders) == 0): ?>
            <!-- No Orders -->
            <div class="text-center mt-5">
                <h1><i class="ri-file-list-3-line me-1"></i></h1>
                <h4>No orders yet!</h4>
                <a href="home.php" class="btn btn-danger mt-3">
                    Order Now
                </a>
            </div>

        <?php else: ?>

            <?php while($order = mysqli_fetch_assoc($orders)): ?>
            <div class="card order-card">

                <!-- Order Header -->
                <div class="order-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                 <?= $order['restaurant_name'] ?>
                            </h6>
                            <small class="text-muted">
                                <?= $order['created_at'] ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <!-- Status Badge -->
                            <?php $status = $order['status']; ?>
                            <?php if($status == 'pending'): ?>
                                <span class="badge bg-warning text-dark">
                                     Pending
                                </span>
                            <?php elseif($status == 'confirmed'): ?>
                                <span class="badge bg-primary">
                                     Confirmed
                                </span>
                            <?php elseif($status == 'preparing'): ?>
                                <span class="badge bg-info">
                                     Preparing
                                </span>
                            <?php elseif($status == 'delivered'): ?>
                                <span class="badge bg-success">
                                     Delivered
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">
                                     Cancelled
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card-body">

                    <?php
                    // Is order ke items fetch karo
                    $items = mysqli_query($conn,
                        "SELECT oi.*, m.name
                         FROM order_items oi
                         JOIN menu_items m ON oi.menu_item_id = m.id
                         WHERE oi.order_id = {$order['id']}");
                    ?>

                    <table class="table table-sm mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($item = mysqli_fetch_assoc($items)): ?>
                            <tr>
                                <td><?= $item['name'] ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>₹<?= $item['price'] ?></td>
                                <td>₹<?= $item['price'] * $item['quantity'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Order Footer -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                 <?= $order['address'] ?>
                            </small>
                        </div>
                        <div>
                            <strong>Total: 
                                <span class="price">
                                    ₹<?= $order['total_amount'] ?>
                                </span>
                            </strong>
                        </div>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>

        <?php endif; ?>

    </div>

</body>
</html>