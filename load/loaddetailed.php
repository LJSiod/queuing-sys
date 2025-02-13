<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {    
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$sort = $_POST['sortby'] ?? 'branchname';
$betweenquery = $_POST['betweenquery'] ?? 'BETWEEN "2024-12-10" AND "' . $currentdate . '"';
$query = 
"SELECT 
    b.id AS branchid, 
    b.branchname,
    SUM(CASE WHEN qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_bs,
    SUM(CASE WHEN qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_dl,
    SUM(CASE WHEN qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_pn,
    SUM(CASE WHEN qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS totalac,
    COUNT(CASE WHEN qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_bs,
    COUNT(CASE WHEN qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_dl,
    COUNT(CASE WHEN qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS noa_pn,
    COUNT(CASE WHEN qi.cashonhandstatus = 'RECEIVED' THEN qi.id ELSE NULL END) AS totalnoa,
    COUNT(CASE WHEN qi.type = 'BS' THEN qi.id ELSE NULL END) AS tas_bs,
    COUNT(CASE WHEN qi.type = 'DL' THEN qi.id ELSE NULL END) AS tas_dl,
    COUNT(CASE WHEN qi.type = 'PN' THEN qi.id ELSE NULL END) AS tas_pn,
    COUNT(qi.id) AS totaltas

  FROM 
    branch b
    LEFT JOIN queueinfo qi ON qi.branchid = b.id
  WHERE 
    qi.date " . $betweenquery . "
  GROUP BY 
    b.id, b.branchname
  ORDER BY 
    " . $sort . " " . ($sort == 'branchname' ? 'ASC' : 'DESC') . "
";

$result = mysqli_query($conn, $query);
$ac_bs = 0;
$ac_dl = 0;
$ac_pn = 0;
$totalac = 0;
$noa_bs = 0;
$noa_dl = 0;
$noa_pn = 0;
$totalnoa = 0;
$tas_bs = 0;
$tas_dl = 0;
$tas_pn = 0;
$totaltas = 0;
?>

<thead>
    <tr class="text-center" title="Click to Sort">
      <th rowspan="3" class="hover" id="branchname">Branch</th>
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
      while ($row = mysqli_fetch_assoc($result)):
        $ac_bs += $row['ac_bs'];
        $ac_dl += $row['ac_dl'];
        $ac_pn += $row['ac_pn'];
        $totalac += $row['totalac'];
        $noa_bs += $row['noa_bs'];
        $noa_dl += $row['noa_dl'];
        $noa_pn += $row['noa_pn'];
        $totalnoa += $row['totalnoa'];
        $tas_bs += $row['tas_bs'];
        $tas_dl += $row['tas_dl'];
        $tas_pn += $row['tas_pn'];
        $totaltas += $row['totaltas'];
      ?>
          <tr class="small">
            <td class="d-none"><?php echo $row['branchid']; ?></td>
            <td class="strong <?php echo ($sort == 'branchname') ? 'text-primary' : ''; ?>"><?php echo $row['branchname']; ?></td>
            <td class="text-right <?php echo ($sort == 'ac_bs') ? 'text-primary' : ''; ?>"><?php echo number_format($row['ac_bs'], 2); ?></td>
            <td class="text-right <?php echo ($sort == 'ac_dl') ? 'text-primary' : ''; ?>"><?php echo number_format($row['ac_dl'], 2); ?></td>
            <td class="text-right <?php echo ($sort == 'ac_pn') ? 'text-primary' : ''; ?>"><?php echo number_format($row['ac_pn'], 2); ?></td>
            <td class="text-right font-weight-bold <?php echo ($sort == 'totalac') ? 'text-primary' : ''; ?>"><?php echo number_format($row['totalac'], 2); ?></td>
            <td class="text-right <?php echo ($sort == 'noa_bs') ? 'text-primary' : ''; ?>"><?php echo $row['noa_bs'] . ' / ' . $row['tas_bs']; ?></td>
            <td class="text-right <?php echo ($sort == 'noa_dl') ? 'text-primary' : ''; ?>"><?php echo $row['noa_dl'] . ' / ' . $row['tas_dl']; ?></td>
            <td class="text-right <?php echo ($sort == 'noa_pn') ? 'text-primary' : ''; ?>"><?php echo $row['noa_pn'] . ' / ' . $row['tas_pn']; ?></td>
            <td class="text-right font-weight-bold <?php echo ($sort == 'totalnoa') ? 'text-primary' : ''; ?>">
                <?php echo $row['totalnoa'] . ' / ' . $row['totaltas'] . ' (' . number_format(($row['totalnoa'] / $row['totaltas']) * 100, 2) . '%)' ; ?>
            </td>
          </tr>
      <?php endwhile; ?>
          <tr>
            <td class="font-weight-bold">Total</td>
            <td class="text-right font-weight-bold"><?php echo number_format($ac_bs, 2); ?></td>
            <td class="text-right font-weight-bold"><?php echo number_format($ac_dl, 2); ?></td>
            <td class="text-right font-weight-bold"><?php echo number_format($ac_pn, 2); ?></td>
            <td class="text-right font-weight-bold"><?php echo number_format($totalac, 2); ?></td>
            <td class="text-right font-weight-bold"><?php echo $noa_bs . ' / ' . $tas_bs; ?></td>
            <td class="text-right font-weight-bold"><?php echo $noa_dl . ' / ' . $tas_dl; ?></td>
            <td class="text-right font-weight-bold"><?php echo $noa_pn . ' / ' . $tas_pn; ?></td>
            <td class="text-right font-weight-bold"><?php echo number_format($totalnoa, 0) . ' / ' . number_format($totaltas, 0) . ' (' . number_format(($totalnoa / $totaltas) * 100, 2) . '%)'; ?></td>
          </tr>
  </tbody>
  
