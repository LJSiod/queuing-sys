<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$querysum1 = "SELECT SUM(cashonhand) AS total1 FROM queueinfo WHERE servedby = 1 AND status = 'DONE' AND cashonhandstatus = 'RECEIVED' AND date = '$currentdate'";
$resultsum1 = mysqli_query($conn, $querysum1);
$rowsum1 = mysqli_fetch_assoc($resultsum1);
$total1 = $rowsum1['total1'];
$total1 = number_format($total1, 2);

$querysum2 = "SELECT SUM(cashonhand) AS total2 FROM queueinfo WHERE servedby = 3 AND status = 'DONE' AND cashonhandstatus = 'RECEIVED' AND date = '$currentdate'";    
$resultsum2 = mysqli_query($conn, $querysum2);
$rowsum2 = mysqli_fetch_assoc($resultsum2);
$total2 = $rowsum2['total2'];
$total2 = number_format($total2, 2);
$overalltotal = $rowsum1['total1'] + $rowsum2['total2'];
$overalltotal = number_format($overalltotal, 2);
?>

<?php 
header("Content-Type: application/json");
echo json_encode([
    'overalltotal' => $overalltotal,
    'total1' => $total1,
    'total2' => $total2
], JSON_UNESCAPED_UNICODE);
?>


