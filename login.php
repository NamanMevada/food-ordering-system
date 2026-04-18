<?php
session_start();
include 'includes/db.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = MD5($_POST['password']);

    $sql    = "SELECT * FROM users WHERE email='$email' AND password='$password' AND status='active'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];


        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($user['role'] == 'restaurant') {
            header("Location: restaurant/dashboard.php");
        } else {
            header("Location: customer/home.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
        }

        .login-box {
            max-width: 430px;
            margin: 80px auto;
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

        .btn-login {
            background: #e74c3c;
            color: white;
            border: none;
        }

        .btn-login:hover {
            background: #c0392b;
            color: white;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="text-center mb-4">
            <div class="brand">FoodHub</div>
            <p class="text-muted">Login to your account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label"> <i class="ri-mail-line"></i>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="ri-lock-2-line"></i>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
            <p class="text-center mt-3">No account? <a href="signup.php">Sign Up here</a></p>
            <hr>
        </form>
    </div>
</body>

</html>