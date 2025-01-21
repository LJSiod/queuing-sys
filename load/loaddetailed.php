<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {    
    header("Location: login.php");
    exit();
}

$view = $_POST['view'] ?? 'Overall';
$sort = $_POST['sortby'] ?? 'branchname';
$currentdate = date('Y-m-d');

$query = "
SELECT 
  b.id AS branchid, 
  b.branchname,
    SUM(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_bs,
    COUNT(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_bs,
    COUNT(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'BS' THEN qi.id ELSE NULL END) AS tas_bs,
    SUM(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_dl,
    COUNT(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_dl,
    COUNT(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'DL' THEN qi.id ELSE NULL END) AS tas_dl,
    SUM(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_pn,
    COUNT(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_pn,
    COUNT(CASE WHEN " . ($view == 'Overall' ? "" : "qi.date = '$currentdate' AND ") . "qi.type = 'PN' THEN qi.id ELSE NULL END) AS tas_pn

FROM 
  branch b
  LEFT JOIN queueinfo qi ON qi.branchid = b.id
GROUP BY 
  b.id, b.branchname
ORDER BY 
  $sort " . ($sort == 'branchname' ? "ASC" : "DESC") . "
";
?>

  <thead>
    <tr style="pointer-events: none;" class="text-center">
      <th rowspan="3"id="branchname" class="<?php echo $sort == 'branchname' ? 'highlight' : ''; ?>">Branch</th>
    </tr>
    <tr style="pointer-events: none;" class="text-center">
      <th id="bsdaily" colspan="4">Amount Collected</th>
      <th id="dldaily" colspan="4">No. of Accounts Settled</th>
      <th id="pndaily" colspan="4">Total Accounts</th>
    </tr>
    <tr>
      <th id="ac_bs">Billing Statement</th>
      <th id="ac_dl">Demand Letter</th>
      <th id="ac_pn">Preliminary Notice</th>
      <th>Total Amount</th>
      <th id="noa_bs">Billing Statement</th>
      <th id="noa_dl">Demand Letter</th>
      <th id="noa_pn">Preliminary Notice</th>
      <th>Total No. of Accts.</th>
      <th id="tas_bs">Billing Statement</th>
      <th id="tas_dl">Demand Letter</th>
      <th id="tas_pn">Preliminary Notice</th>
      <th>Total Accts.</th>      
    </tr>
  </thead>
  <tbody>
      <?php
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)):
      ?>
          <tr class="small">
            <td class="d-none"><?php echo $row['branchid']; ?></td>
            <td class="strong <?php echo $sort == 'branchname' ? 'highlight' : ''; ?>"><?php echo $row['branchname']; ?></td>
            <td class="text-right <?php echo $sort == 'totaltoday' ? 'highlight' : ''; ?>"><?php echo number_format($row['ac_bs'], 2); ?></td>
            <td class="text-right <?php echo $sort == 'totaltoday' ? 'highlight' : ''; ?>"><?php echo number_format($row['ac_dl'], 2); ?></td>
            <td class="text-right <?php echo $sort == 'totaltoday' ? 'highlight' : ''; ?>"><?php echo number_format($row['ac_pn'], 2); ?></td>
            <td class="text-right font-weight-bold"><?php echo number_format($row['ac_bs'] + $row['ac_dl'] + $row['ac_pn'], 2); ?></td>
            <td class="text-right <?php echo $sort == 'paidtoday' ? 'highlight' : ''; ?>"><?php echo $row['noa_bs']; ?></td>
            <td class="text-right <?php echo $sort == 'paidtoday' ? 'highlight' : ''; ?>"><?php echo $row['noa_dl']; ?></td>
            <td class="text-right <?php echo $sort == 'paidtoday' ? 'highlight' : ''; ?>"><?php echo $row['noa_pn']; ?></td>
            <td class="text-right font-weight-bold"><?php echo $row['noa_bs'] + $row['noa_dl'] + $row['noa_pn']; ?></td>
            <td class="text-right <?php echo $sort == 'totalaccountstoday' ? 'highlight' : ''; ?>"><?php echo $row['tas_bs']; ?></td>
            <td class="text-right <?php echo $sort == 'totalaccountstoday' ? 'highlight' : ''; ?>"><?php echo $row['tas_dl']; ?></td>
            <td class="text-right <?php echo $sort == 'totalaccountstoday' ? 'highlight' : ''; ?>"><?php echo $row['tas_pn']; ?></td>
            <td class="text-right font-weight-bold"><?php echo $row['tas_bs'] + $row['tas_dl'] + $row['tas_pn']; ?></td>
          </tr>
      <?php endwhile; ?>
      <tr class="text-center small font-weight-bold">
        <td>Total</td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
        <td class="text-right font-weight-bold"></td>
      </tr>
  </tbody>
