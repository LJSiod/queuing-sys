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
  date DESC";

$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
  $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
      $bs_noatas = $row['noa_bs'] . ' / ' .  $row['tas_bs'];
      $dl_noatas = $row['noa_dl'] . ' / ' .  $row['tas_dl'];
      $pn_noatas = $row['noa_pn'] . ' / ' .  $row['tas_pn'];
      $total_noatas = $row['totalnoa'] . ' / ' .  $row['totaltas'] ;
        $data[] = array(
            'branchid' => $branchid,
            'date' => $row['date'],
            'ac_bs' => number_format($row['ac_bs'], 2),
            'ac_dl' => number_format($row['ac_dl'], 2),
            'ac_pn' => number_format($row['ac_pn'], 2),
            'totalac' => number_format($row['totalac'], 2),
            'bs_noatas' => $bs_noatas,
            'dl_noatas' => $dl_noatas,
            'pn_noatas' => $pn_noatas,
            'totalnoatas' => $total_noatas,
        );
    }
    echo json_encode(array('data' => $data));
} else {
    echo json_encode(array('data' => array()));
}
