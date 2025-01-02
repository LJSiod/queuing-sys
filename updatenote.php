<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');


if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$cashonhand = $_POST['cashonhand'];
$fullname = $_SESSION['fullname'];
$id = $_POST['id'];
$note = $_POST['note'];


$query = "UPDATE queueinfo SET note = '$note', cashonhand = '$cashonhand' WHERE id = $id";

if (mysqli_query($conn, $query)) {
    exit();
}

mysqli_close($conn);
?>

