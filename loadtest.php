<?php
session_start();
include 'config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$branch_id = $_SESSION['branch_id'];
$currentdate = date('Y-m-d');
$query =
    "SELECT 
        qi.id, 
        qi.queueno, 
        qi.branchid, 
        qi.type, 
        qi.clientname, 
        qi.loanamount, 
        qi.totalbalance, 
        qi.cashonhand, 
        qi.cashonhandstatus, 
        qi.datereceived, 
        qi.status, 
        qi.date, 
        b.branchname 
    FROM 
        queueinfo qi 
        LEFT JOIN branch b ON qi.branchid = b.id 
    WHERE 
        qi.stat = 'ACTIVE' ORDER BY qi.id DESC"
;
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'id' => $row['id'],
            'queueno' => $row['queueno'],
            'branchname' => $row['branchname'],
            'type' => $row['type'],
            'clientname' => strtoupper($row['clientname']),
            'loanamount' => number_format($row['loanamount'], 2),
            'totalbalance' => number_format($row['totalbalance'], 2),
            'cashonhand' => number_format($row['cashonhand'], 2),
            'cashonhandstatus' => $row['cashonhandstatus'],
            'datereceived' => date('Y-m-d', strtotime($row['datereceived'])),
            'status' => $row['status'],
            'date' => date('Y-m-d', strtotime($row['date'])),
        );
    }
    echo json_encode(array('data' => $data));
} else {
    echo json_encode(array('data' => array()));
}
?>