<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$query = "SELECT DATE(date) AS date, SUM(cashonhand) AS totalperday, COUNT(cashonhand) AS paidperday 
FROM queueinfo WHERE cashonhandstatus = 'RECEIVED' 
GROUP BY DATE(date) ORDER BY DATE(date) DESC";
$result = mysqli_query($conn, $query);


if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'date' => $row['date'],
            'dateformatted' => $row['date'] == $currentdate ? 'Today' : date('M d, Y', strtotime($row['date'])),
            'totalperday' => number_format($row['totalperday'], 2),
            'paidperday' => $row['paidperday']
        );
    }
    echo json_encode(array('data' => $data));
} else {
    echo json_encode(array('data' => array()));
}
