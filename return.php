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
$ql = $_POST['id'];


$query = "UPDATE queueinfo SET status = 'IN QUEUE', servedby = '$id' WHERE id = $ql";

if (mysqli_query($conn, $query)) {
    header("Location: admin_dashboard.php");
    $logger->log($ql . ' Returned to Queue by: ' . $fullname);
    exit();
} else {
    echo "Error Serving: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

