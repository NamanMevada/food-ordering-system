<?php
include '../includes/auth.php';
checkRole('restaurant');
include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Check karo already registered hai ya nahi
$check = mysqli_query($conn, 
    "SELECT id FROM restaurants WHERE user_id=$user_id");
if(mysqli_num_rows($check) > 0){
    header("Location: dashboard.php");
    exit();
}

// Form submit hua?
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    if(empty($name) || empty($phone) || empty($address)){
        $error = "Please fill all required fields!";
    } else {
        $sql = "INSERT INTO restaurants 
                (user_id, name, phone, address, description, status) 
                VALUES 
                ('$user_id','$name','$phone','$address','$description','pending')";
        
        if(mysqli_query($conn, $sql)){
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Restaurant - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .form-box {
            max-width: 550px;
            margin: 60px auto;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .brand { color: #e74c3c; font-weight: 800; font-size: 24px; }
        .btn-submit { background: #e74c3c; color: white; border: none; }
        .btn-submit:hover { background: #c0392b; color: white; }
    </style>
</head>
<body>
<div class="form-box">
    <div class="text-center mb-4">
        <div class="brand">FoodHub</div>
        <h5 class="mt-2">Register Your Restaurant</h5>
        <p class="text-muted">Fill details — Admin will approve soon!</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Restaurant Name *</label>
            <input type="text" name="name" 
                   class="form-control" 
                   placeholder="eg. Pizza Palace" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone Number *</label>
            <input type="text" name="phone" 
                   class="form-control" 
                   placeholder="eg. 9876543210" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address *</label>
            <textarea name="address" 
                      class="form-control" 
                      rows="2" 
                      placeholder="Restaurant full address" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" 
                      class="form-control" 
                      rows="3" 
                      placeholder="Tell customers about your restaurant..."></textarea>
        </div>
        <button type="submit" class="btn btn-submit w-100">
            Submit for Approval
        </button>
        <p class="text-center mt-3">
            <a href="dashboard.php">← Back to Dashboard</a>
        </p>
    </form>
</div>
</body>
</html>