<?php
include '../includes/auth.php';
checkRole('customer');
include '../includes/db.php';

// Sirf approved restaurants lao
$restaurants = mysqli_query(
    $conn,
    "SELECT * FROM restaurants 
     WHERE status='approved' 
     ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #eef2f6);
        }
         .navbar {
    padding: 14px 30px;
}
        .navbar-brand {
            color: #e74c3c !important;
            font-weight: 800;
            font-size: 24px;
        }

        .hero-section {
            background: url('https://images.unsplash.com/photo-1505253716362-afaea1d3d1af') center/cover no-repeat;
            height: 160px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-section .overlay {
            background: rgba(0, 0, 0, 0.6);
            width: 100%;
            height: 100%;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .restaurant-card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            transition: 0.3s;
        }

        .restaurant-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
         .card-img-top {
    height: 200px;
    object-fit: cover;
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
    <nav class="navbar shadow-sm px-4" style="background:#2c3e50;">
        <a class="navbar-brand">
            <span style="color:white;">Food</span><span style="color:#ff4757;">Hub</span>
        </a>
        <div>
            <a href="orders.php" class="btn btn-outline-light me-2">
                <i class="ri-file-list-3-line me-1"></i>
                My Orders
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

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="hero-section mb-4">
            <div class="overlay text-center text-white">
                <h1 style="font-weight:800; font-size:42px; letter-spacing:1px;">
                    <i class="ri-user-3-line me-2"></i>
                    Welcome, <?= $_SESSION['user_name']; ?>!
                    </h2>
                    <p style="font-size:18px; opacity:0.9;">
                        Order delicious food from your favourite restaurants
                    </p>
            </div>
        </div>
        <hr style="opacity:0.1;">

        <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;">
            <i class="ri-store-2-line me-2"></i>Explore Restaurants
        </h4>

        <div class="row">
            <?php while ($r = mysqli_fetch_assoc($restaurants)): ?>
                <div class="col-md-4">
                    <div class="card restaurant-card">
                        <img src="../assets/images/<?= $r['image'] ?>" class="card-img-top">
                        <div class="card-body">

                            <h5 class="card-title"><?= $r['name'] ?></h5>
                            <p class="text-muted mb-1">
                                <i class="ri-map-pin-2-line me-1"></i>
                                <?= $r['address'] ?>
                            </p>
                            <p class="text-muted mb-3">
                                <i class="ri-phone-line me-1"></i>
                                <?= $r['phone'] ?>
                            </p>
                            <p>
                                <i class="ri-file-text-line me-1"></i>
                                <?= $r['description'] ?>
                            </p>
                            <a href="restaurant.php?id=<?= $r['id'] ?>"
                                class="btn btn-order w-100">
                                <i class="ri-restaurant-line me-1"></i> View Menu
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

</body>

</html>