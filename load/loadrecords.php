<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
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
        qi.stat = 'ACTIVE' " . 
        ($branch_id == 8 ? "" : "AND qi.branchid = '$branch_id' ")
        . " ORDER BY qi.id DESC"
        ;
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'id' => $row['id'],
            'queueno' =>'<span class="label">Queue No: </span>' . $row['queueno'],
            'branchname' =>'<span class="label">Branch: </span>' . $row['branchname'],
            'type' =>'<span class="label">Type: </span>' . $row['type'],
            'clientname' =>'<span class="label">Client Name: </span>' . strtoupper($row['clientname']),
            'loanamount' =>'<span class="label">Loan Amount: </span>' . number_format($row['loanamount'], 2),
            'totalbalance' =>'<span class="label">Total Balance: </span>' . number_format($row['totalbalance'], 2),
            'cashonhand' =>'<span class="label">On-hand Cash: </span>' . number_format($row['cashonhand'], 2),
            'cashonhandstatus' => '<span class="label">COH Status: </span>' . 
                ($row['cashonhandstatus'] == 'RECEIVED' ? '<span class="text-success">RECEIVED</span>' : 
                ($row['cashonhandstatus'] == 'PENDING' ? '<span class="text-warning">PENDING</span>' : '<span class="text-danger">DECLINED</span>')),
            'datereceived' =>'<span class="label">Date Letter Received: </span>' . date('Y-m-d', strtotime($row['datereceived'])),
            'status' =>'<span class="label">Status: </span>' . $row['status'],
            'date' =>'<span class="label">Date: </span>' . date('Y-m-d', strtotime($row['date'])),
        );
    }
    echo json_encode(array('data' => $data));
} else {
    echo json_encode(array('data' => array()));
}

