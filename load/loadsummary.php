<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {    
    header("Location: login.php");
    exit();
}

$sort = $_POST['sortby'] ?? 'branchname';
$currentdate = date('Y-m-d');
$query = "
    SELECT 
        b.id AS branchid, 
        b.branchname,
        SUM(IF(qi.date = '$currentdate' AND qi.cashonhandstatus = 'RECEIVED', qi.cashonhand, 0)) AS totaltoday,
        COUNT(IF(qi.date = '$currentdate' AND qi.cashonhandstatus = 'RECEIVED', qi.id, NULL)) AS paidtoday,
        COUNT(IF(qi.date = '$currentdate', qi.id, NULL)) AS totalaccountstoday,
        SUM(IF(qi.cashonhandstatus = 'RECEIVED', qi.cashonhand, 0)) AS total,
        COUNT(IF(qi.cashonhandstatus = 'RECEIVED', qi.id, NULL)) AS paid,
        COUNT(qi.id) AS totalaccounts
    FROM branch b
    LEFT JOIN queueinfo qi ON qi.branchid = b.id
    GROUP BY b.id, b.branchname
    ORDER BY $sort " . ($sort == 'branchname' ? "ASC" : "DESC") . "
";
?>

  <thead title="Click to Sort">
      <tr class="text-center">
          <th rowspan="2" id="branchname" class="hover" onclick="loadoverall('branchname')">Branch</th>
          <th colspan="3">Daily</th>
          <th colspan="3">Overall</th>
      </tr>
      <tr>
          <th id="totaltoday" class="hover" onclick="loadoverall('totaltoday')">Amount Collected</th>
          <th id="paidtoday" class="hover" onclick="loadoverall('paidtoday')">No. of Accounts Settled</th>
          <th id="totalaccountstoday" class="hover" onclick="loadoverall('totalaccountstoday')">Total Accounts</th>
          <th id="total" class="hover" onclick="loadoverall('total')">Amount Collected</th>
          <th id="paid" class="hover" onclick="loadoverall('paid')">No. of Accounts Settled</th>
          <th id="totalaccounts" class="hover" onclick="loadoverall('totalaccounts')">Total Accounts</th>
      </tr>
  </thead>
  <tbody title="Right-Click to view list">
      <?php
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)):
      ?>
          <tr class="small">
              <td class="d-none"><?php echo $row['branchid']; ?></td>
              <td class="strong <?php echo $sort == 'branchname' ? 'text-primary' : ''; ?>"><p class="label">Branch: </p><?php echo $row['branchname']; ?></td>
              <td class="text-right <?php echo $sort == 'totaltoday' ? 'text-primary' : ''; ?>"><p class="label">Amount Collected(Daily): </p><?php echo number_format($row['totaltoday'], 2); ?></td>
              <td class="text-right <?php echo $sort == 'paidtoday' ? 'text-primary' : ''; ?>"><p class="label">No. of Accounts Settled(Daily): </p><?php echo $row['paidtoday']; ?></td>
              <td class="text-right <?php echo $sort == 'totalaccountstoday' ? 'text-primary' : ''; ?>"><p class="label">Total Accounts(Daily): </p><?php echo $row['totalaccountstoday']; ?></td>
              <td class="text-right <?php echo $sort == 'total' ? 'text-primary' : ''; ?>"><p class="label">Amount Collected(Overall): </p><?php echo number_format($row['total'], 2); ?></td>
              <td class="text-right <?php echo $sort == 'paid' ? 'text-primary' : ''; ?>"><p class="label">No. of Accounts Settled(Overall): </p><?php echo $row['paid']; ?></td>
              <td class="text-right <?php echo $sort == 'totalaccounts' ? 'text-primary' : ''; ?>"><p class="label">Total Accounts(Overall): </p><?php echo $row['totalaccounts']; ?></td>
          </tr>
      <?php endwhile; ?>
  </tbody>
