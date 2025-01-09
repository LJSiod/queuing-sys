<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {    
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$query = "SELECT SUM(qi.cashonhand) AS total, COUNT(qi.id) AS paid FROM queueinfo qi WHERE qi.cashonhandstatus = 'RECEIVED'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$querydaily = "SELECT SUM(qi.cashonhand) AS total, COUNT(qi.id) AS paid FROM queueinfo qi WHERE qi.cashonhandstatus = 'RECEIVED' AND qi.date = '$currentdate'";
$resultdaily = mysqli_query($conn, $querydaily);
$rowdaily = mysqli_fetch_assoc($resultdaily);
?>

<tbody>
  <tr>
    <td class="font-weight-bold" rowspan="2">Amount Collected</td>
    <td class="font-weight-bold small">Overall</td>
    <td class="font-weight-bold text-right small"><?php echo number_format($row['total'], 2); ?></td>
  </tr>
  <tr>
    <td class="font-weight-bold small">Daily</td>
    <td class="font-weight-bold text-right small"><?php echo number_format($rowdaily['total'], 2); ?></td>
  </tr>
  <tr>
    <td class="font-weight-bold" rowspan="2">No. of Accounts Settled</td>
    <td class="font-weight-bold small">Overall</td>
    <td class="font-weight-bold text-right small"><?php echo $row['paid']; ?></td>
  </tr>
  <tr>
    <td class="font-weight-bold small">Daily</td>
    <td class="font-weight-bold text-right small"><?php echo $rowdaily['paid']; ?></td>
  </tr>
</tbody>
