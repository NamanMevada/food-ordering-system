<?php
include '../includes/auth.php';
checkRole('customer');
include '../includes/db.php';

// Sirf approved restaurants lao
$restaurants = mysqli_query($conn,
    "SELECT * FROM restaurants 
     WHERE status='approved' 
     ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar-brand { 
            color: #e74c3c !important; 
            font-weight: 800; 
            font-size: 24px; 
        }
        .restaurant-card {
            border-radius: 12px;
            transition: 0.3s;
            margin-bottom: 25px;
        }
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .btn-order {
            background: #e74c3c;
            color: white;
            border: none;
        }
        .btn-order:hover { 
            background: #c0392b; 
            color: white; 
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-white shadow-sm px-4">
        <a class="navbar-brand">FoodHub</a>
        <div>
            <a href="orders.php" class="btn btn-outline-danger me-2">
                My Orders
            </a>
            <a href="cart.php" class="btn btn-outline-danger me-2">
                Cart
            </a>
            <a href="../logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;">All Restaurants</h4>

        <div class="row">
        <?php while($r = mysqli_fetch_assoc($restaurants)): ?>
            <div class="col-md-4">
                <div class="card restaurant-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $r['name'] ?></h5>
                        <p class="text-muted mb-1">
                            <?= $r['address'] ?>
                        </p>
                        <p class="text-muted mb-3">
                            <?= $r['phone'] ?>
                        </p>
                        <p><?= $r['description'] ?></p>
                        <a href="restaurant.php?id=<?= $r['id'] ?>" 
                           class="btn btn-order w-100">
                            View Menu →
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

    </div>

</body>
</html>