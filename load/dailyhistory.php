<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
// $query = "SELECT DATE(date) AS date,
// SUM(IF(cashonhandstatus = 'RECEIVED', cashonhand, 0)) AS totalperday,
// COUNT(id) AS totalaccts
// FROM queueinfo
// WHERE date = DATE(date)
// GROUP BY DATE(date) ORDER BY DATE(date) DESC";
$query = "SELECT 
  date(qi.date) as date,
    SUM(CASE WHEN qi.type = 'BS' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_bs,
    SUM(CASE WHEN qi.type = 'DL' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_dl,
    SUM(CASE WHEN qi.type = 'PN' AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS ac_pn,
    SUM(CASE WHEN qi.type IN ('BS', 'DL', 'PN') AND qi.cashonhandstatus = 'RECEIVED' THEN qi.cashonhand ELSE 0 END) AS totalac,
    COUNT(CASE WHEN qi.type = 'BS' THEN qi.id ELSE NULL END) AS tas_bs,
    COUNT(CASE WHEN qi.type = 'DL' THEN qi.id ELSE NULL END) AS tas_dl,
    COUNT(CASE WHEN qi.type = 'PN' THEN qi.id ELSE NULL END) AS tas_pn,
    COUNT(CASE WHEN qi.type IN ('BS', 'DL', 'PN') THEN qi.id ELSE NULL END) AS totaltas

FROM 
  branch b
  LEFT JOIN queueinfo qi ON qi.branchid = b.id
GROUP BY 
  DATE(date)
ORDER BY 
  date DESC";
$result = mysqli_query($conn, $query);



if (mysqli_num_rows($result) > 0) {
    $data = array();
    // while ($row = mysqli_fetch_assoc($result)) {
    //     $data[] = array(
    //         'date' => $row['date'],
    //         'dateformatted' => $row['date'] == $currentdate ? 'Today' : date('M d, Y', strtotime($row['date'])),
    //         'totalperday' => number_format($row['totalperday'], 2),
    //         'paidperday' => $row['totalaccts']
    //     );
    // }
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'dateformatted' => $row['date'] == $currentdate ? 'Today' : date('M d, Y', strtotime($row['date'])),
            'totaltas' => $row['totaltas'],
            'tas_pn' => $row['tas_pn'],
            'tas_dl' => $row['tas_dl'],
            'tas_bs' => $row['tas_bs'],
            'totalac' => number_format($row['totalac'], 2),
            'ac_pn' => number_format($row['ac_pn'], 2),
            'ac_dl' => number_format($row['ac_dl'], 2),
            'ac_bs' => number_format($row['ac_bs'], 2),
        );
    }
    echo json_encode(array('data' => $data));
} else {
    echo json_encode(array('data' => array()));
}
