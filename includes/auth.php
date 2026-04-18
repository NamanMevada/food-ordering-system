<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Usage: pass required role
function checkRole($required_role) {
    if ($_SESSION['user_role'] != $required_role) {
        header("Location: ../login.php");
        exit();
    }
}
?>

