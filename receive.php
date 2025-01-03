<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';
include 'logger.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}
$fullname = $_SESSION['fullname'];
$id = $_SESSION['user_id'];
$note = $_POST['note'];
$branchid = $_POST['branchid'];
$ql = $_POST['id'];

$query = "UPDATE queueinfo SET cashonhandstatus = 'RECEIVED', status = 'DONE', note = '$note' WHERE id = $ql";

if (mysqli_query($conn, $query)) {
    $logger->log($branchid);
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "Error Serving: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

