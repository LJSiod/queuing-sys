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
        SUM(IF(qi.cashonhandstatus = 'RECEIVED', qi.cashonhand, 0)) AS total,
        COUNT(IF(qi.date = '$currentdate' AND qi.cashonhandstatus = 'RECEIVED', qi.id, NULL)) AS paidtoday,
        COUNT(IF(qi.cashonhandstatus = 'RECEIVED', qi.id, NULL)) AS paid,
        COUNT(IF(qi.date = '$currentdate', qi.id, NULL)) AS totalaccountstoday,
        COUNT(qi.id) AS totalaccounts
    FROM branch b
    LEFT JOIN queueinfo qi ON qi.branchid = b.id
    GROUP BY b.id, b.branchname
    ORDER BY $sort " . ($sort == 'branchname' ? "ASC" : "DESC") . "
";
?>

  <thead>
      <tr style="pointer-events: none;" class="text-center">
          <th rowspan="2" id="branchname" class="<?php echo $sort == 'branchname' ? 'highlight' : ''; ?>">Branch</th>
          <th colspan="3">Daily</th>
          <th colspan="3">Overall</th>
      </tr>
      <tr style="pointer-events: none;">
          <th id="totaltoday" class="<?php echo $sort == 'totaltoday' ? 'highlight' : ''; ?>">Amount Collected</th>
          <th id="paidtoday" class="<?php echo $sort == 'paidtoday' ? 'highlight' : ''; ?>">No. of Accounts Settled</th>
          <th id="totalaccountstoday" class="<?php echo $sort == 'totalaccountstoday' ? 'highlight' : ''; ?>">Total Accounts</th>
          <th id="total" class="<?php echo $sort == 'total' ? 'highlight' : ''; ?>">Amount Collected</th>
          <th id="paid" class="<?php echo $sort == 'paid' ? 'highlight' : ''; ?>">No. of Accounts Settled</th>
          <th id="totalaccounts" class="<?php echo $sort == 'totalaccounts' ? 'highlight' : ''; ?>">Total Accounts</th>
      </tr>
  </thead>
  <tbody title="Right-Click to view list">
      <?php
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)):
      ?>
          <tr class="small">
              <td class="d-none"><?php echo $row['branchid']; ?></td>
              <td class="strong <?php echo $sort == 'branchname' ? 'highlight' : ''; ?>"><p class="label">Branch: </p><?php echo $row['branchname']; ?></td>
              <td class="text-right <?php echo $sort == 'totaltoday' ? 'highlight' : ''; ?>"><p class="label">Amount Collected(Daily): </p><?php echo number_format($row['totaltoday'], 2); ?></td>
              <td class="text-right <?php echo $sort == 'paidtoday' ? 'highlight' : ''; ?>"><p class="label">No. of Accounts Settled(Daily): </p><?php echo $row['paidtoday']; ?></td>
              <td class="text-right <?php echo $sort == 'totalaccountstoday' ? 'highlight' : ''; ?>"><p class="label">Total Accounts(Daily): </p><?php echo $row['totalaccountstoday']; ?></td>
              <td class="text-right <?php echo $sort == 'total' ? 'highlight' : ''; ?>"><p class="label">Amount Collected(Overall): </p><?php echo number_format($row['total'], 2); ?></td>
              <td class="text-right <?php echo $sort == 'paid' ? 'highlight' : ''; ?>"><p class="label">No. of Accounts Settled(Overall): </p><?php echo $row['paid']; ?></td>
              <td class="text-right <?php echo $sort == 'totalaccounts' ? 'highlight' : ''; ?>"><p class="label">Total Accounts(Overall): </p><?php echo $row['totalaccounts']; ?></td>
          </tr>
      <?php endwhile; ?>
  </tbody>
