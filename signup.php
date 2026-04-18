<?php

include 'includes/db.php';

$error = "";
$success = "";



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = MD5($_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);



    $chek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($chek) > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$password', '$role')";

        if (mysqli_query($conn, $sql)) {
            $success = "Sign Up successfully! Please login.";
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
    <title>Sign Up - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        body {
            background: #f8f9fa;
        }

        .register-box {
            max-width: 480px;
            margin: 60px auto;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .brand {
            color: #e74c3c;
            font-weight: 800;
            font-size: 28px;
        }

        .form-label i {
            margin-right: 6px;
        }

        .btn-register {
            background: #e74c3c;
            color: white;
            border: none;
        }

        .btn-register:hover {
            background: #c0392b;
            color: white;
        }
    </style>

</head>

<body>



    <div class="register-box">
        <div class="text-center mb-4">
            <div class="brand">
                FoodHub
            </div>
            <p class="text-muted">Create your account</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>



        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label"><i class="ri-user-3-line"></i>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="ri-mail-line"></i>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="ri-lock-2-line"></i>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="ri-user-settings-line"></i>Account Type</label>
                <select name="role" class="form-select" required>
                    <option value=""> Select Role </option>
                    <option value="customer">Customer</option>
                    <option value="restaurant">Restaurant Owner</option>
                </select>
            </div>
            <button type="submit" class="btn btn-register w-100">Sign Up</button>
            <p class="text-center mt-3">Already have account? <a href="login.php">Login</a></p>

        </form>
</body>

</html>