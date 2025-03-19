<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}

$branchid = $_SESSION['branch_id'];
$currentdate = date('Y-m-d');
$query = "SELECT 
    SUM(qi.cashonhand) AS total, 
    COUNT(qi.id) AS paid, 
    (SELECT COUNT(id) FROM queueinfo WHERE branchid = '$branchid') AS totalaccounts 
FROM queueinfo qi 
WHERE qi.cashonhandstatus = 'RECEIVED' 
AND qi.branchid = '$branchid'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$querydaily = "SELECT 
    SUM(qi.cashonhand) AS total, 
    COUNT(qi.id) AS paid, 
    (SELECT COUNT(id) FROM queueinfo WHERE date = '$currentdate' AND branchid = '$branchid') AS totalaccounts 
FROM queueinfo qi 
WHERE qi.cashonhandstatus = 'RECEIVED' 
AND qi.date = '$currentdate' 
AND qi.branchid = '$branchid'";
$resultdaily = mysqli_query($conn, $querydaily);
$rowdaily = mysqli_fetch_assoc($resultdaily);
?>

<span class="small font-weight-bold">Amount Collected Overall:</span><span class="font-weight-bold small"
    style="float:right;"><?php echo number_format($row['total'], 2); ?></span><br>
<span class="small font-weight-bold">Amount Collected Daily:</span><span class="font-weight-bold small"
    style="float:right;"><?php echo number_format($rowdaily['total'], 2); ?></span><br>
<span class="small font-weight-bold">Total Accounts Overall:</span><span class="font-weight-bold small"
    style="float:right;"><?php echo number_format($row['totalaccounts'], 0); ?></span><br>
<span class="small font-weight-bold">Total Accounts Daily:</span><span class="font-weight-bold small"
    style="float:right;"><?php echo number_format($rowdaily['totalaccounts'], 0); ?></span>