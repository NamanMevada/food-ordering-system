<?php

include '../includes/auth.php';
checkRole('customer');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$error   = "";
$success = "";



if (isset($_GET['action']) && isset($_GET['item_id'])) {

    $item_id = $_GET['item_id'];
    $action  = $_GET['action'];

    if ($action == 'increase') {

        mysqli_query(
            $conn,
            "UPDATE cart SET quantity = quantity + 1
             WHERE user_id=$user_id AND menu_item_id=$item_id"
        );
    } elseif ($action == 'decrease') {

        $check = mysqli_query(
            $conn,
            "SELECT quantity FROM cart
             WHERE user_id=$user_id AND menu_item_id=$item_id"
        );

        $row = mysqli_fetch_assoc($check);

        if ($row['quantity'] <= 1) {

            mysqli_query(
                $conn,
                "DELETE FROM cart
                 WHERE user_id=$user_id AND menu_item_id=$item_id"
            );
        } else {

            mysqli_query(
                $conn,
                "UPDATE cart SET quantity = quantity - 1
                 WHERE user_id=$user_id AND menu_item_id=$item_id"
            );
        }
    } elseif ($action == 'remove') {

        mysqli_query(
            $conn,
            "DELETE FROM cart
             WHERE user_id=$user_id AND menu_item_id=$item_id"
        );
    }

    header("Location: cart.php");
    exit();
}





if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $address = mysqli_real_escape_string($conn, $_POST['address']);

    if (empty($address)) {

        $error = "Please enter delivery address!";
    } else {

        $cart_items = mysqli_query(
            $conn,
            "SELECT c.*, m.price, m.restaurant_id
             FROM cart c
             JOIN menu_items m ON c.menu_item_id = m.id
             WHERE c.user_id=$user_id"
        );

        if (mysqli_num_rows($cart_items) == 0) {

            $error = "Your cart is empty!";
        } else {

            $total       = 0;
            $items_array = [];
            $rest_id     = 0;

            while ($item = mysqli_fetch_assoc($cart_items)) {

                $total  += $item['price'] * $item['quantity'];
                $rest_id = $item['restaurant_id'];
                $items_array[] = $item;
            }

            $order_sql = "INSERT INTO orders 
                          (customer_id, restaurant_id, total_amount, address, status)
                          VALUES 
                          ('$user_id','$rest_id','$total','$address','pending')";

            if (mysqli_query($conn, $order_sql)) {

                $order_id = mysqli_insert_id($conn);

                foreach ($items_array as $item) {

                    mysqli_query(
                        $conn,
                        "INSERT INTO order_items 
                         (order_id, menu_item_id, quantity, price)
                         VALUES 
                         ('$order_id','{$item['menu_item_id']}',
                          '{$item['quantity']}','{$item['price']}')"
                    );
                }

                mysqli_query(
                    $conn,
                    "DELETE FROM cart WHERE user_id=$user_id"
                );

                header("Location: orders.php");
                exit();
            } else {

                $error = "Something went wrong!";
            }
        }
    }
}





$cart_items = mysqli_query(
    $conn,
    "SELECT c.*, m.name, m.price, m.description, m.image,
        r.name AS restaurant_name
 FROM cart c
 JOIN menu_items m ON c.menu_item_id = m.id
 JOIN restaurants r ON m.restaurant_id = r.id
 WHERE c.user_id=$user_id"
);


$grand_total = 0;
$cart_array  = [];

while ($item = mysqli_fetch_assoc($cart_items)) {

    $grand_total += $item['price'] * $item['quantity'];
    $cart_array[] = $item;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar-brand {
            color: #e74c3c !important;
            font-weight: 800;
            font-size: 24px;
        }

        .cart-card {
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .price {
            color: #e74c3c;
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

        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            font-weight: 800;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-white shadow-sm px-4">
        <a class="navbar-brand">FoodHub</a>
        <div>
            <a href="home.php" class="btn btn-outline-danger me-2">
                Home
            </a>
            <a href="orders.php" class="btn btn-outline-danger me-2">
                My Orders
            </a>
            <a href="../logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;"> My Cart</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (count($cart_array) == 0): ?>
            <!-- Cart Empty -->
            <div class="text-center mt-5">
                <h1><i class="ri-shopping-cart-2-line"></i></h1>
                <h4>Your cart is empty!</h4>
                <a href="home.php" class="btn btn-danger mt-3">
                    Browse Restaurants
                </a>
            </div>
            

        <?php else: ?>
            <div class="row">

                <!-- Cart Items -->
                <div class="col-md-8">
                    <?php foreach ($cart_array as $item): ?>
                        <div class="card cart-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($item['image']): ?>
                                        <img src="../assets/images/<?= $item['image'] ?>"
                                            width="60" height="60"
                                            style="border-radius:10px; object-fit:cover;">
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0"><?= $item['name'] ?></h6>
                                        <small class="text-muted">
                                            <?= $item['restaurant_name'] ?>
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Quantity Controls -->
                                        <a href="?action=decrease&item_id=<?= $item['menu_item_id'] ?>"
                                            class="btn btn-sm btn-outline-danger quantity-btn">-</a>
                                        <span class="mx-2"><?= $item['quantity'] ?></span>
                                        <a href="?action=increase&item_id=<?= $item['menu_item_id'] ?>"
                                            class="btn btn-sm btn-danger quantity-btn">+</a>

                                        <!-- Item Total -->
                                        <span class="price ms-3">
                                            ₹<?= $item['price'] * $item['quantity'] ?>
                                        </span>

                                        <!-- Remove -->
                                        <a href="?action=remove&item_id=<?= $item['menu_item_id'] ?>"
                                            class="btn btn-sm btn-outline-danger ms-2"
                                            onclick="return confirm('Remove this item?')">
                                            Remove
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card cart-card">
                        <div class="card-body">
                            <h5 class="mb-3">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>₹<?= $grand_total ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Delivery</span>
                                <span class="text-success">FREE</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong class="price">₹<?= $grand_total ?></strong>
                            </div>

                            <!-- Address + Place Order -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Delivery Address
                                    </label>
                                    <textarea name="address"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Enter your delivery address..."
                                        required></textarea>
                                </div>
                                <button type="submit" class="btn btn-order w-100">
                                    Place Order
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>

</body>

</html>