<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'config/db.php';
if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$branchid = $_SESSION['branch_id'];
$tmpName = $_FILES['file']['tmp_name'];
$fileName = $_FILES['file']['name'];
$fileType = $_FILES['file']['type'];
$fileSize = $_FILES['file']['size'];
$uploadDir = 'ledger/';
$currentDate = date('Y-m-d');
$uploadFile = $uploadDir . $branchid . "_" . $fileName;

function getNextQueueNo($conn) {
        $currentDate = date('Y-m-d');
        $query = "SELECT queueno FROM queueinfo WHERE date = '$currentDate' ORDER BY id DESC LIMIT 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $lastQueueNo = intval($row['queueno']);
            $nextQueueNo = str_pad($lastQueueNo + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextQueueNo = '001';
        }
      
        return $nextQueueNo;
    }

$queueno = getNextQueueNo($conn);
$branch_id = $_SESSION['branch_id'];
$type = mysqli_real_escape_string($conn, $_POST['type']);
$clientname = mysqli_real_escape_string($conn, $_POST['clientname']);
$loanamount = mysqli_real_escape_string($conn, $_POST['loanamount']);
$datereleased = mysqli_real_escape_string($conn, $_POST['datereleased']);
$maturitydate = mysqli_real_escape_string($conn, $_POST['maturitydate']);
$remainingbalance = mysqli_real_escape_string($conn, $_POST['remainingbalance']);
$onhand = mysqli_real_escape_string($conn, $_POST['onhand']);
$contactno = mysqli_real_escape_string($conn, $_POST['contactno']);
$accinterest = mysqli_real_escape_string($conn, $_POST['accinterest']);
$totalbalance = mysqli_real_escape_string($conn, $_POST['totalbalance']);
$remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

if (move_uploaded_file($tmpName, $uploadFile)) {
    $query = "INSERT INTO queueinfo (branchid, queueno, type, clientname, loanamount, datereleased, maturitydate, totalbalance, cashonhand, activenumber, attachname, remarks, date, status, stat, cashonhandstatus) 
    VALUES ('$branch_id', '$queueno', '$type', '$clientname', '$loanamount', '$datereleased', '$maturitydate', '$remainingbalance', '$onhand', '$contactno', '$uploadFile', '$remarks', '$currentDate', 'IN QUEUE', 'ACTIVE', 'PENDING')";
    mysqli_query($conn, $query);
     echo json_encode(array('success' => true, 'message' => 'File uploaded successfully!'));
    exit;
} else {
    echo json_encode(array('success' => false, 'message' => 'There was an error uploading the file.'));
    exit;
}


mysqli_close($conn);
}

?>


