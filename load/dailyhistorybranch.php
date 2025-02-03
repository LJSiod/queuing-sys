<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$branchid = $_SESSION['branch_id'];
$query = "SELECT 
  date(qi.date) as date,
    SUM(CASE WHEN qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_bs,
    SUM(CASE WHEN qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_dl,
    SUM(CASE WHEN qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_pn,
    SUM(CASE WHEN qi.type IN ('BS', 'DL', 'PN') AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS totalac,
    COUNT(CASE WHEN qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_bs,
    COUNT(CASE WHEN qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_dl,
    COUNT(CASE WHEN qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_pn,
    COUNT(CASE WHEN qi.type IN ('BS', 'DL', 'PN') AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS totalnoa,
    COUNT(CASE WHEN qi.type = 'BS' THEN qi.id ELSE NULL END) AS tas_bs,
    COUNT(CASE WHEN qi.type = 'DL' THEN qi.id ELSE NULL END) AS tas_dl,
    COUNT(CASE WHEN qi.type = 'PN' THEN qi.id ELSE NULL END) AS tas_pn,
    COUNT(CASE WHEN qi.type IN ('BS', 'DL', 'PN') THEN qi.id ELSE NULL END) AS totaltas

FROM 
  branch b
  LEFT JOIN queueinfo qi ON qi.branchid = b.id
 where qi.branchid = '$branchid'
GROUP BY 
  DATE(date)
ORDER BY 
  DATE(date) DESC";

$result = mysqli_query($conn, $query);
?>

    <thead>
    <tr class="text-center" title="Click to Sort">
      <th rowspan="3" class="hover" id="date">Date</th>
    </tr>
    <tr style="pointer-events: none;" class="text-center">
      <th class="hover" id="bsdaily" colspan="4">Amount Collected</th>
      <th class="hover" id="dldaily" colspan="4">No. of Accounts Settled/Total Accounts</th>
    </tr>
    <tr title="Click to Sort">
      <th class="hover" id="ac_bs">Billing Statement</th>
      <th class="hover" id="ac_dl">Demand Letter</th>
      <th class="hover" id="ac_pn">Preliminary Notice</th>
      <th class="hover" id="totalac">Total Amount</th>
      <th class="hover" id="noa_bs">Billing Statement</th>
      <th class="hover" id="noa_dl">Demand Letter</th>
      <th class="hover" id="noa_pn">Preliminary Notice</th>
      <th class="hover" id="totalnoa">Total</th>
    </tr>
  </thead>
  <tbody> 
      <?php
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)):
      ?>
          <tr class="small <?php if ($row['date'] == $currentdate) { echo 'today'; } ?>">
            <td class="d-none"><?php echo $row['branchid']; ?></td>
            <td class="strong"><?php if ($row['date'] == $currentdate) { echo 'Today'; } else { echo date('M d, Y', strtotime($row['date'])); } ?></td>
            <td class="text-right <?php echo ($sort == 'ac_bs') ? 'text-primary' : ''; ?>"><?php echo number_format($row['ac_bs'], 2); ?></td>
            <td class="text-right <?php echo ($sort == 'ac_dl') ? 'text-primary' : ''; ?>"><?php echo number_format($row['ac_dl'], 2); ?></td>
            <td class="text-right <?php echo ($sort == 'ac_pn') ? 'text-primary' : ''; ?>"><?php echo number_format($row['ac_pn'], 2); ?></td>
            <td class="text-right font-weight-bold <?php echo ($sort == 'totalac') ? 'text-primary' : ''; ?>"><?php echo number_format($row['totalac'], 2); ?></td>
            <td class="text-right <?php echo ($sort == 'noa_bs') ? 'text-primary' : ''; ?>"><?php echo $row['noa_bs'] . ' / ' . $row['tas_bs']; ?></td>
            <td class="text-right <?php echo ($sort == 'noa_dl') ? 'text-primary' : ''; ?>"><?php echo $row['noa_dl'] . ' / ' . $row['tas_dl']; ?></td>
            <td class="text-right <?php echo ($sort == 'noa_pn') ? 'text-primary' : ''; ?>"><?php echo $row['noa_pn'] . ' / ' . $row['tas_pn']; ?></td>
            <td class="text-right font-weight-bold <?php echo ($sort == 'totalnoa') ? 'text-primary' : ''; ?>"><?php echo $row['totalnoa'] . ' / ' . $row['totaltas']; ?></td>
          </tr>
      <?php endwhile; ?>
      <!-- <tr class="text-center small font-weight-bold">
        <td>Total</td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
      </tr> -->
  </tbody>
