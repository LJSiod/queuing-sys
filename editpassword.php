<?php
session_start();
include 'db.php';
include 'logger.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
$id = $_SESSION['user_id'];
$password = $_POST['password'];
$query = "UPDATE users SET password = '$password' WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: admin_dashboard.php");
    $logger->log('Password Updated to ' . $password . ' by: ' . $fullname);
    exit();
} else {
    echo "Error updating record: " . mysqli_error($conn);
}