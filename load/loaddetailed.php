<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
  header("Location: ../login.php");
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
    <th class="hover" id="dldaily" colspan="4">Total Accounts Settled</th>
    <th class="hover" id="bsdaily" colspan="4">Amount Collected</th>
  </tr>
  <tr title="Click to Sort">
    <th class="hover" id="ac_bs">Billing Statement</th>
    <th class="hover" id="ac_dl">Demand Letter</th>
    <th class="hover" id="ac_pn">Preliminary Notice</th>
    <th class="hover" id="totalac">Total</th>
    <th class="hover" id="tas_bs">Billing Statement</th>
    <th class="hover" id="tas_dl">Demand Letter</th>
    <th class="hover" id="tas_pn">Preliminary Notice</th>
    <th class="hover" id="totaltas">Total</th>
  </tr>
</thead>
<tbody>
  <?php
  while ($row = mysqli_fetch_assoc($result)):
    $tas_bs += $row['tas_bs'];
    $tas_dl += $row['tas_dl'];
    $tas_pn += $row['tas_pn'];
    $totaltas += $row['totaltas'];
    $ac_bs += $row['ac_bs'];
    $ac_dl += $row['ac_dl'];
    $ac_pn += $row['ac_pn'];
    $totalac += $row['totalac'];
    ?>
    <tr class="small">
      <td class="d-none"><?php echo $row['branchid']; ?></td>
      <td class="strong <?php echo ($sort == 'branchname') ? 'text-primary' : ''; ?>"><?php echo $row['branchname']; ?>
      </td>
      <td class="text-right <?php echo ($sort == 'noa_bs') ? 'text-primary' : ''; ?>"><?php echo $row['tas_bs']; ?></td>
      <td class="text-right <?php echo ($sort == 'noa_dl') ? 'text-primary' : ''; ?>"><?php echo $row['tas_dl']; ?></td>
      <td class="text-right <?php echo ($sort == 'noa_pn') ? 'text-primary' : ''; ?>"><?php echo $row['tas_pn']; ?></td>
      <td class="text-right font-weight-bold <?php echo ($sort == 'totalnoa') ? 'text-primary' : ''; ?>">
        <?php echo $row['totaltas']; ?></td>
      <td class="text-right <?php echo ($sort == 'ac_bs') ? 'text-primary' : ''; ?>">
        <?php echo number_format($row['ac_bs'], 2); ?></td>
      <td class="text-right <?php echo ($sort == 'ac_dl') ? 'text-primary' : ''; ?>">
        <?php echo number_format($row['ac_dl'], 2); ?></td>
      <td class="text-right <?php echo ($sort == 'ac_pn') ? 'text-primary' : ''; ?>">
        <?php echo number_format($row['ac_pn'], 2); ?></td>
      <td class="text-right font-weight-bold <?php echo ($sort == 'totalac') ? 'text-primary' : ''; ?>">
        <?php echo number_format($row['totalac'], 2); ?></td>
    </tr>
  <?php endwhile; ?>
  <tr>
    <td class="small font-weight-bold">Total</td>
    <td class="small text-right font-weight-bold"><?php echo $tas_bs; ?></td>
    <td class="small text-right font-weight-bold"><?php echo $tas_dl; ?></td>
    <td class="small text-right font-weight-bold"><?php echo $tas_pn; ?></td>
    <td class="small text-right font-weight-bold"><?php echo number_format($totaltas, 0); ?></td>
    <td class="small text-right font-weight-bold"><?php echo number_format($ac_bs, 2); ?></td>
    <td class="small text-right font-weight-bold"><?php echo number_format($ac_dl, 2); ?></td>
    <td class="small text-right font-weight-bold"><?php echo number_format($ac_pn, 2); ?></td>
    <td class="small text-right font-weight-bold"><?php echo number_format($totalac, 2); ?></td>
  </tr>
</tbody>