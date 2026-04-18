<?php
include '../includes/auth.php';
checkRole('restaurant');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$error   = "";
$success = "";

$chek = mysqli_query($conn, "SELECT * FROM restaurants WHERE user_id=$user_id");
$restaurant = mysqli_fetch_assoc($chek);

if (!$restaurant || $restaurant['status'] != 'approved') {
    header("Location: dashboard.php");
    exit();
}


$rest_id = $restaurant['id'];


if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$del_id AND restaurant_id=$rest_id");
    header("Location: menu.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        $upload_path = '../assets/images/' . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
    }

    if (empty($name) || empty($price)) {
        $error = "Name and Price are required!";
    } else {
        $sql = "INSERT INTO menu_items 
                (restaurant_id, category_id, name, description, price, image) 
                VALUES 
                ('$rest_id','$category_id','$name','$description','$price','$image_name')";
        if (mysqli_query($conn, $sql)) {
            $success = "Menu item added!";
        } else {
            $error = "Something went wrong!";
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories");


$menu_items = mysqli_query(
    $conn,
    "SELECT m.*, c.name AS category_name 
     FROM menu_items m
     JOIN categories c ON m.category_id = c.id
     WHERE m.restaurant_id = $rest_id
     ORDER BY m.created_at DESC"
);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Menu Items - FoodHub</title>
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
                <h4 class="mb-4"style="font-family: 'Poppins', sans-serif;">Manage Menu Items</h4>

                <div class="card mb-4">
                    <div class="card-header"><b>Add New Item</b></div>
                    <div class="card-body">

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Item Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="eg. Margherita pizza" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price (₹) </label>
                                    <input type="number" name="price" class="form-control" placeholder="eg. 199" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Select Category</option>
                                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                            <option value="<?= $cat['id'] ?>">
                                                <?= $cat['name'] ?>
                                            </option>
                                        <?php endwhile; ?>

                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Item description..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item Image</label>
                                <input type="file" name="image"
                                    class="form-control"
                                    accept="image/*">
                                <small class="text-muted">
                                    JPG, PNG, JPEG allowed
                                </small>
                            </div>
                            <button type="submit" class="btn btn-danger">+ Add Item</button>
                        </form>

                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><b>All Menu Items</b></div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                while ($item = mysqli_fetch_assoc($menu_items)):
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>

                                        <td>
                                            <?php if ($item['image']): ?>
                                                <img src="../assets/images/<?= $item['image'] ?>"
                                                    width="50" height="50"
                                                    style="border-radius:8px; object-fit:cover;">
                                            <?php endif; ?>
                                            <?= $item['name'] ?><br>
                                            <small class="text-muted"><?= $item['description'] ?></small>
                                        </td>

                                        <td><?= $item['category_name'] ?></td>
                                        <td>₹<?= $item['price'] ?></td>
                                        <td>
                                            <a href="?delete=<?= $item['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this item?')">
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
        </div>
    </div>
</body>

</html>