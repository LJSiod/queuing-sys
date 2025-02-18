<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'config/db.php';
include 'logger.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_POST['id'];
$action = $_POST['action'];
$note = $_POST['note'];
$cashonhand = $_POST['cashonhand'];
$password = $_POST['password'];
$username = $_POST['username'];
$branchid = $_SESSION['branch_id'];
$userid = $_SESSION['user_id'];

switch ($action) {
    case 'decline':
        $query = "UPDATE queueinfo SET cashonhandstatus = 'DECLINED', status = 'DONE', note = '$note' WHERE id = $id";
        $log = $id . " Declined by: " . $_SESSION['fullname'] . ", Note: '" . $note . "'";
        break;
    case 'receive':
        $query = "UPDATE queueinfo SET cashonhandstatus = 'RECEIVED', status = 'DONE', note = '$note' WHERE id = $id";
        $log = $id . " Received by: " . $_SESSION['fullname'] . ", Note: '" . $note . "'";
        break;
    case 'updatereceived':
        $query = "UPDATE queueinfo SET cashonhandstatus = 'RECEIVED' WHERE id = $id";
        $log = $id . " Status Updated to RECEIVED by: " . $_SESSION['fullname'];
        break;
    case 'updatedeclined':
        $query = "UPDATE queueinfo SET cashonhandstatus = 'DECLINED' WHERE id = $id";
        $log = $id . " Status Updated to DECLINED by: " . $_SESSION['fullname'];
        break;
    case 'return':
        $query = "UPDATE queueinfo SET status = 'IN QUEUE', servedby = '$userid' WHERE id = $id";
        $log = $id . " Returned by: " . $_SESSION['fullname'];
        break;
    case 'serve':
        $query = "UPDATE queueinfo SET status = 'SERVING', servedby = '$userid' WHERE id = $id";
        $log = $id . " Served by: " . $_SESSION['fullname'];
        break;
    case 'password':
        $query = "UPDATE users SET password = '$password' WHERE id = $userid";
        $log = $_SESSION['fullname'] . " updated their password";
        break;
    case 'username':
        $query = "UPDATE users SET username = '$username' WHERE id = $userid";
        $log = $_SESSION['fullname'] . " updated their username";
        break;
    case 'note':
        $query = "UPDATE queueinfo SET note = '$note', cashonhand = '$cashonhand' WHERE id = $id";
        $log = $_SESSION['fullname'] . " updated " . $id . "'s note to: '" . $note . "'" . ", Cash on Hand: '" . $cashonhand . "'";
        break;
    default:
        echo "Invalid action";
        exit();
}

if (mysqli_query($conn, $query)) {
    $logger->log($log);
    header("Location: views/dashboard.php");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
