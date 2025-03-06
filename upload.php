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
$frontTmp = $_FILES['file']['tmp_name'];
$frontName = $_FILES['file']['name'];
$frontType = $_FILES['file']['type'];
$frontSize = $_FILES['file']['size'];
$backTmp = $_FILES['file1']['tmp_name'];
$backName = $_FILES['file1']['name'];
$backType = $_FILES['file1']['type'];
$backSize = $_FILES['file1']['size'];
$uploadDir = 'ledger/';
$currentDate = date('Y-m-d');
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
$frontFile = $uploadDir . $branchid . $queueno . "_" . $frontName;
if ($backName == "") {
    $backFile = null;
} else {
    $backFile = $uploadDir . $branchid . $queueno . "_" . $backName;

}
$branch_id = $_SESSION['branch_id'];
$type = mysqli_real_escape_string($conn, $_POST['type']);
$clientname = mysqli_real_escape_string($conn, $_POST['clientname']);
$loanamount = mysqli_real_escape_string($conn, $_POST['loanamount']);
$datereleased = mysqli_real_escape_string($conn, $_POST['datereleased']);
$maturitydate = mysqli_real_escape_string($conn, $_POST['maturitydate']);
$remainingbalance = mysqli_real_escape_string($conn, $_POST['remainingbalance']);
$onhand = mysqli_real_escape_string($conn, $_POST['onhand']);
$daterec = mysqli_real_escape_string($conn, $_POST['daterec']);
$accinterest = mysqli_real_escape_string($conn, $_POST['accinterest']);
$totalbalance = mysqli_real_escape_string($conn, $_POST['totalbalance']);
$remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

if (empty($backFile)) {
    if (move_uploaded_file($frontTmp, $frontFile)) {
        $query = "INSERT INTO queueinfo (branchid, queueno, type, clientname, loanamount, datereleased, maturitydate, totalbalance, cashonhand, datereceived, front, back, remarks, date, status, stat, cashonhandstatus) 
        VALUES ('$branch_id', '$queueno', '$type', '$clientname', '$loanamount', '$datereleased', '$maturitydate', '$remainingbalance', '$onhand', '$daterec', '$frontFile', '$backFile', '$remarks', '$currentDate', 'IN QUEUE', 'ACTIVE', 'PENDING')";
        mysqli_query($conn, $query);
         echo json_encode(array('success' => true, 'message' => 'File uploaded successfully!'));
        exit;
    } else {
        echo json_encode(array('success' => false, 'message' => 'There was an error uploading the file.'));
        exit;
    }
} else {
    if (move_uploaded_file($frontTmp, $frontFile) && move_uploaded_file($backTmp, $backFile)) {
        $query = "INSERT INTO queueinfo (branchid, queueno, type, clientname, loanamount, datereleased, maturitydate, totalbalance, cashonhand, datereceived, front, back, remarks, date, status, stat, cashonhandstatus) 
        VALUES ('$branch_id', '$queueno', '$type', '$clientname', '$loanamount', '$datereleased', '$maturitydate', '$remainingbalance', '$onhand', '$daterec', '$frontFile', '$backFile', '$remarks', '$currentDate', 'IN QUEUE', 'ACTIVE', 'PENDING')";
        mysqli_query($conn, $query);
         echo json_encode(array('success' => true, 'message' => 'File uploaded successfully!'));
        exit;
    } else {
        echo json_encode(array('success' => false, 'message' => 'There was an error uploading the file.'));
        exit;
    }
}

mysqli_close($conn);
}

?>


