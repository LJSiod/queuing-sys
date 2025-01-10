<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {    
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$query = "
    SELECT 
        b.id AS branchid, 
        b.branchname,
        SUM(IF(qi.date = '$currentdate', qi.cashonhand, 0)) AS totaltoday,
        SUM(qi.cashonhand) AS total,
        COUNT(IF(qi.date = '$currentdate' AND qi.cashonhandstatus = 'RECEIVED', qi.id, NULL)) AS paidtoday,
        COUNT(IF(qi.cashonhandstatus = 'RECEIVED', qi.id, NULL)) AS paid,
        COUNT(IF(qi.date = '$currentdate', qi.id, NULL)) AS totalaccountstoday,
        COUNT(qi.id) AS totalaccounts
    FROM branch b
    LEFT JOIN queueinfo qi ON qi.branchid = b.id
    GROUP BY b.id, b.branchname
    ORDER BY total DESC
";
?>

  <thead>
      <tr style="pointer-events: none;">
          <th rowspan="2">Branch</th>
          <th colspan="3">Daily</th>
          <th colspan="3">Overall</th>
      </tr>
      <tr style="pointer-events: none;">
          <th>Amount Collected</th>
          <th>No. of Accounts Settled</th>
          <th>Total Accounts</th>
          <th>Amount Collected</th>
          <th>No. of Accounts Settled</th>
          <th>Total Accounts</th>
      </tr>
  </thead>
  <tbody title="Right-Click to view list">
      <?php
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)):

      ?>
          <tr class="small">
              <td class="d-none"><?php echo $row['branchid']; ?></td>
              <td class="strong"><p class="label">Branch: </p><?php echo $row['branchname']; ?></td>
              <td class="text-right"><p class="label">Amount Collected(Daily): </p><?php echo number_format($row['totaltoday'], 2); ?></td>
              <td class="text-right"><p class="label">No. of Accounts Settled(Daily): </p><?php echo $row['paidtoday']; ?></td>
              <td class="text-right"><p class="label">Total Accounts(Daily): </p><?php echo $row['totalaccountstoday']; ?></td>
              <td class="text-right"><p class="label">Amount Collected(Overall): </p><?php echo number_format($row['total'], 2); ?></td>
              <td class="text-right"><p class="label">No. of Accounts Settled(Overall): </p><?php echo $row['paid']; ?></td>
              <td class="text-right"><p class="label">Total Accounts(Overall): </p><?php echo $row['totalaccounts']; ?></td>
          </tr>
      <?php endwhile; ?>
  </tbody>
