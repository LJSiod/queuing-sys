<?php
session_start();
include 'db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}
$fullname = $_SESSION['fullname'];
$id = $_SESSION['user_id'];
$note = $_POST['note'];
$ql = $_POST['id'];

$query = "UPDATE queueinfo SET cashonhandstatus = 'RECEIVED', status = 'DONE', note = '$note' WHERE id = $ql";

if (mysqli_query($conn, $query)) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "Error Serving: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

