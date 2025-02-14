<?php
session_start();
include '../config/db.php';
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
    qi.clientname, 
    qi.remarks, 
    qi.note, 
    qi.cashonhandstatus, 
    qi.status, 
    qi.date, 
    b.branchname 
    FROM queueinfo qi 
    LEFT JOIN branch b ON qi.branchid = b.id 
    WHERE note IS NOT NULL AND (qi.branchid = '$branch_id' OR '$branch_id' = 8)
    ORDER BY qi.id DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'idpreview' => $row['id'],
            'id' => '<span class="label">ID: </span>' . $row['id'],
            'queueno' => '<span class="label">Queue No: </span>' . $row['queueno'],
            'branchname' => '<span class="label">Branch: </span>' . $row['branchname'],
            'clientname' => '<span class="label">Client Name: </span>' . strtoupper($row['clientname']),
            'remarks' => '<span class="label">Remarks: </span>' . $row['remarks'],
            'note' => '<span class="label">Note: </span>' . $row['note'],
            'cashonhandstatus' => '<span class="label">Status: </span>' . 
                ($row['cashonhandstatus'] == 'RECEIVED' ? '<span class="text-success">RECEIVED</span>' : 
                ($row['cashonhandstatus'] == 'PENDING' ? '<span class="text-warning">PENDING</span>' : '<span class="text-danger">DECLINED</span>')),
            'status' => $row['status'],
            'date' =>'<span class="label">Date: </span>' . date('Y-m-d', strtotime($row['date'])),
        );
    }
    echo json_encode(array('data' => $data));
} else {
    echo json_encode(array('data' => array()));
}
?>
