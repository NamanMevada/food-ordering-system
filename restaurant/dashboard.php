<?php


include '../includes/auth.php';
checkRole('restaurant');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$chek = mysqli_query($conn, "SELECT * FROM restaurants WHERE user_id=$user_id");
$restaurant = mysqli_fetch_assoc($chek);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard - FoodHub</title>
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

        .stat-card {
            border-radius: 12px;
            padding: 25px;
            color: white;
            margin-bottom: 20px;
        }

        .stat-card h2 {
            font-size: 40px;
            font-weight: 800;
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
                <?php
                if ($restaurant):
                ?>
                    <a href="menu.php"><i class="ri-menu-line"></i>Menu Items</a>
                    <a href="orders.php"><i class="ri-shopping-bag-3-fill"></i>Orders</a>
                <?php
                endif;
                ?>
                <a href="../logout.php"><i class="ri-logout-box-line"></i>Logout</a>
            </div>

            <div class="col-md-10 p-4">
                <h4 style="font-family: 'Poppins', sans-serif;">Welcome, <?= $_SESSION['user_name'] ?></h4>

                <?php if (!$restaurant): ?>

                    <div class="alert alert-warning mt-4">
                        <h5>You have not registered your restaurant yet!</h5>
                        <p>Register your restaurant to start receiving orders.</p>
                        <a href="register-restaurant.php" class="btn btn-danger">
                            + Register Restaurant
                        </a>
                    </div>

                <?php elseif ($restaurant['status'] == 'pending'): ?>

                    <div class="alert alert-info mt-4">

                        <h5>Your restaurant is under review!</h5>
                        <p>Admin will approve your restaurant soon.</p>

                    </div>

                <?php elseif ($restaurant['status'] == 'rejected'): ?>

                    <div class="alert alert-danger mt-4">

                        <h5>Your restaurant was rejected!</h5>
                        <p>Please contact admin for more information.</p>

                    </div>

                <?php else: ?>

                    <p class="text-muted"><?= $restaurant['name'] ?></p>

                    <?php

                    $rest_id = $restaurant['id'];

                    $total_menu = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM menu_items WHERE restaurant_id=$rest_id"));

                    $total_orders = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE restaurant_id=$rest_id"));


                    $pending_orders = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE restaurant_id=$rest_id AND status='pending'"));



                    ?>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="stat-card" style="background:#e74c3c;">
                                <p>Menu Items</p>
                                <h2><?= $total_menu ?></h2>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background:#2ecc71;">
                                <p>Total Orders</p>
                                <h2><?= $total_orders ?></h2>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background:#f39c12;">
                                <p>Pending Orders</p>
                                <h2><?= $pending_orders ?></h2>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>

</html>