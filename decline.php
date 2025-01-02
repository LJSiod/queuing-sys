<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}
$fullname = $_SESSION['fullname'];
$id = $_SESSION['user_id'];
$ql = $_POST['id'];
$note = $_POST['note'];

$query = "UPDATE queueinfo SET cashonhandstatus = 'DECLINED', note = '$note', status = 'DONE' WHERE id = $ql";

if (mysqli_query($conn, $query)) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "Error Serving: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

