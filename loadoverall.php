<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {    
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$query = "SELECT b.id AS branchid, b.branchname, COALESCE(SUM(qi.cashonhand), 0) AS total, COALESCE(COUNT(qi.id), 0) AS paid 
          FROM branch b 
          LEFT JOIN queueinfo qi ON qi.branchid = b.id AND qi.cashonhandstatus = 'RECEIVED'
          GROUP BY b.id, b.branchname ORDER BY total DESC";
?>

  <thead>
      <tr>
          <th>Branch</th>
          <th>Amount</th>
          <th>Paid</th>
      </tr>
  </thead>
  <tbody title="Right-Click to view list">
      <?php
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)):
      ?>
          <tr class="small">
              <td class="d-none"><?php echo $row['branchid']; ?></td>
              <td><p class="label">Branch: </p><?php echo $row['branchname']; ?></td>
              <td class="text-right"><p class="label">Amount: </p><?php echo number_format($row['total'], 2); ?></td>
              <td class="text-right"><p class="label">Paid: </p><?php echo $row['paid']; ?></td>
          </tr>
      <?php endwhile; ?>
  </tbody>
