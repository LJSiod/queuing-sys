<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$branch_id = $_SESSION['branch_id'];
$currentdate = date('Y-m-d');
$filterdate = "";
$query = "";
if (isset($_GET['filterdate'])) {
    $filterdate = $_GET['filterdate'];
    if ($branch_id == 8) {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.clientname, qi.remarks, qi.note, qi.cashonhandstatus, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE note IS NOT NULL and date = '$filterdate' ORDER BY qi.id DESC";
    } else {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.clientname, qi.remarks, qi.note, qi.cashonhandstatus, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.branchid = '$branch_id' AND note IS NOT NULL and date = '$filterdate' ORDER BY qi.id DESC";
    }
} else {
    if ($branch_id == 8) {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.clientname, qi.remarks, qi.note, qi.cashonhandstatus, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE note IS NOT NULL ORDER BY qi.id DESC";
    } else {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.clientname, qi.remarks, qi.note, qi.cashonhandstatus, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.branchid = '$branch_id' AND note IS NOT NULL ORDER BY qi.id DESC";
    }
}
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td class="d-none"><?php echo $row['id']; ?></td>
            <td><p class="label">Queue no.: </p><?php echo $row['queueno']; ?></td>
            <td><p class="label">Branch: </p><?php echo $row['branchname']; ?></td>
            <td><p class="label">Client Name: </p><?php echo strtoupper($row['clientname']); ?></td>
            <td><p class="label">Remarks: </p><?php echo $row['remarks']; ?></td>
            <td><p class="label">Note: </p><?php echo $row['note']; ?></td>
            <td class="<?php echo ($row['cashonhandstatus'] === 'RECEIVED') ? 'text-success' : (($row['cashonhandstatus'] === 'DECLINED') ? 'text-danger' : ''); ?>"><p class="label">Status: </p><?php echo $row['cashonhandstatus']; ?></td>
            <td><p class="label">Date: </p><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
        </tr>
    <?php endwhile;
} else {
    echo '<tr style="pointer-events: none;"><td colspan="7" class="text-left">No records found.</td></tr>';
}
?>
