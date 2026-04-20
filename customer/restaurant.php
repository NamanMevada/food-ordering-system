<?php
include '../includes/auth.php';
checkRole('customer');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];


if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}
$rest_id = $_GET['id'];

$rest_result = mysqli_query(
    $conn,
    "SELECT * FROM restaurants 
     WHERE id=$rest_id AND status='approved'"
);
$restaurant = mysqli_fetch_assoc($rest_result);

if (!$restaurant) {
    header("Location: home.php");
    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $item_id = $_GET['item_id'];

    $check = mysqli_query(
        $conn,
        "SELECT * FROM cart 
         WHERE user_id=$user_id AND menu_item_id=$item_id"
    );

    if (mysqli_num_rows($check) > 0) {

        mysqli_query(
            $conn,
            "UPDATE cart SET quantity = quantity + 1 
             WHERE user_id=$user_id AND menu_item_id=$item_id"
        );
    } else {

        mysqli_query(
            $conn,
            "INSERT INTO cart (user_id, menu_item_id, quantity) 
             VALUES ('$user_id', '$item_id', 1)"
        );
    }
    header("Location: restaurant.php?id=$rest_id");
    exit();
}


$menu_items = mysqli_query(
    $conn,
    "SELECT m.*, c.name AS category_name
     FROM menu_items m
     JOIN categories c ON m.category_id = c.id
     WHERE m.restaurant_id=$rest_id 
     AND m.status='available'
     ORDER BY m.created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $restaurant['name'] ?> - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar {
    padding: 14px 30px;
}

        .navbar-brand {
            color: #e74c3c !important;
            font-weight: 800;
            font-size: 24px;
        }

        .rest-info {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .item-card {
            border-radius: 12px;
            transition: 0.3s;
            margin-bottom: 25px;
        }

        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .price {
            color: #e74c3c;
            font-size: 22px;
            font-weight: 800;
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


    <nav class="navbar shadow-sm px-4" style="background:#2c3e50;">
        <a class="navbar-brand">
    <span style="color:white;">Food</span><span style="color:#ff4757;">Hub</span>
</a>
        <div>
            <a href="orders.php" class="btn btn-outline-light me-2">
                My Orders
            </a>
            <a href="cart.php" class="btn btn-outline-light me-2">
                Cart
            </a>
            <a href="home.php" class="btn btn-outline-light me-2">
                Home
            </a>
            <a href="../logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>
    </nav>

    <div class="container mt-4">

        <div class="rest-info">
            <h3><?= $restaurant['name'] ?></h3>
            <p class="text-muted mb-1"> <?= $restaurant['address'] ?></p>
            <p class="text-muted mb-0"> <?= $restaurant['phone'] ?></p>
            <?php if ($restaurant['description']): ?>
                <p class="mt-2"><?= $restaurant['description'] ?></p>
            <?php endif; ?>
        </div>

        <h5 class="mb-3"> Menu</h5>
        <div class="row">
            <?php while ($item = mysqli_fetch_assoc($menu_items)): ?>
                <div class="col-md-4">
                    <div class="card item-card">
                        <?php if($item['image']): ?>
        <img src="../assets/images/<?= $item['image'] ?>" 
             class="card-img-top"
             style="height:200px; object-fit:cover;">
    <?php else: ?>
        <img src="../assets/images/default.png" 
             class="card-img-top"
             style="height:200px; object-fit:cover;">
    <?php endif; ?>
                        <div class="card-body">
                            <span class="badge bg-danger mb-2">
                                <?= $item['category_name'] ?>
                            </span>
                            <h5><?= $item['name'] ?></h5>
                            <p class="text-muted">
                                <?= $item['description'] ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price">₹<?= $item['price'] ?></span>
                                <a href="?id=<?= $rest_id ?>&action=add&item_id=<?= $item['id'] ?>"
                                    class="btn btn-order">
                                    + Add to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

</body>

</html>