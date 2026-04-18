<?php

include '../includes/auth.php';
checkRole('admin');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FoodHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>

        body { background: #f8f9fa; }
        

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
        .sidebar a:hover { background: #e74c3c; }
        .sidebar .brand {
            color: white;
            font-size: 22px;
            font-weight: 800;
            padding: 10px 20px 25px;
            border-bottom: 1px solid #455a64;
            margin-bottom: 10px;
        }
         .sidebar .brand span {
    color: #ff4757; /* red color */
}
        .stat-card {
            border-radius: 12px;
            padding: 25px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-card h2 { font-size: 40px; font-weight: 800; }

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
       <a href="restaurants.php"><i class="ri-restaurant-2-fill"></i>Restaurant</a>
       <a href="users.php"><i class="ri-group-fill"></i>users</a>
       <a href="categories.php"><i class="ri-menu-line"></i>Categories</a>
       <a href="../logout.php"><i class="ri-logout-box-line"></i>Logout</a>
   </div>
      
   <div class="col-md-10 p-4">
     <h4 class="mb-4" style="font-family: 'Poppins', sans-serif;">Welcome, <?=$_SESSION['user_name']?> !</h4>


   <?php
      include '../includes/db.php';

      $total_users = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM users WHERE role='customer'"));

      $total_restaurants = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM restaurants WHERE status='approved'"));

      $pending_restaurants =  mysqli_num_rows(mysqli_query($conn,"SELECT id FROM restaurants WHERE status='pending'"));

      $total_orders = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM orders"));
   ?>

   <div class="row">

        <div class="col-md-3">
            <div class="stat-card" style="background:#e74c3c;">
               <p>Total Customer</p>
               <h2><?= $total_users ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:#2ecc71;">
               <p>Active Restaurant</p>
               <h2><?= $total_restaurants ?></h2>
            </div>
        </div>
         <div class="col-md-3">
            <div class="stat-card" style="background:#f39c12;">
               <p>pending Approvals</p>
               <h2><?= $pending_restaurants ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card" style="background:#9b59b6;">
               <p>Total orders</p>
               <h2><?= $total_orders ?></h2>
            </div>
        </div>

   </div>

   <div class="card mt-3">
      <div class="card-header">
        <b>
            Recent Restaurant Requests
        </b>
      </div>
      <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        <tbody>

            <?php
              $restaurants = mysqli_query($conn,
                    
                "SELECT 
                        r.*,
                        u.name AS owner_name
                FROM 
                      restaurants r
                JOIN
                     users u
                     ON r.user_id = u.id  
                ORDER BY
                    r.created_at DESC
                LIMIT 5"

              );
              while($r=mysqli_fetch_assoc($restaurants)): ?>
             
             <tr>
                <td><?= $r['name'] ?><br>
                <small class="text-muted">by <?= $r['owner_name'] ?></small>
                </td>
                <td><?= $r['phone'] ?></td>
                <td>
                    <?php if($r['status'] == 'approved'): ?>
                        <span class="badge bg-success">Approved</span>
                    <?php elseif($r['status'] == 'pending'): ?>
                        <span class="badge bg-warning">Pending</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="restaurants.php" class="btn btn-sm btn-primary">Manage</a>
                </td>
             </tr>

             <?php endwhile; ?>

        </tbody>
    </table>
         </div>
      </div>
   </div>
 </div>  
</body>
</html>