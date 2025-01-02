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
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.cashonhand, qi.cashonhandstatus, qi.activenumber, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.date = '$filterdate' ORDER BY qi.id DESC";
    } else {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.cashonhand, qi.cashonhandstatus, qi.activenumber, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.branchid = '$branch_id' AND qi.date = '$filterdate' ORDER BY qi.id DESC";
    }
} else {
    if ($branch_id == 8) {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.cashonhand, qi.cashonhandstatus, qi.activenumber, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id ORDER BY qi.id DESC";
    } else {
        $query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.cashonhand, qi.cashonhandstatus, qi.activenumber, qi.status, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.branchid = '$branch_id' ORDER BY qi.id DESC";
    }
}


$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td class="d-none"><?php echo $row['id']; ?></td>
            <td class="d-none"><?php echo $row['cashonhandstatus']; ?></td>
            <td><p class="label">Queue no.: </p><?php echo $row['queueno']; ?></td>
            <td><p class="label">Branch: </p><?php echo $row['branchname']; ?></td>
            <td><p class="label">Type: </p><?php echo $row['type']; ?></td>
            <td><p class="label">Client Name: </p><?php echo strtoupper($row['clientname']); ?></td>
            <td><p class="label">Loan Amount: </p><?php echo number_format($row['loanamount'], 2); ?></td>
            <td><p class="label">Total Balance: </p><?php echo number_format($row['totalbalance'], 2); ?></td>
            <td><p class="label">Cash on Hand: </p><?php echo number_format($row['cashonhand'], 2); ?></td>
            <td class="<?php echo ($row['cashonhandstatus'] === 'RECEIVED') ? 'text-success' : (($row['cashonhandstatus'] === 'DECLINED') ? 'text-danger' : ''); ?>">
                <p class="label">Cash on Hand Status: </p><?php echo $row['cashonhandstatus']; ?>
            </td>
            <td><p class="label">Active Number: </p><?php echo $row['activenumber']; ?></td>
            <td><p class="label">Status: </p><?php echo $row['status']; ?></td>
            <td><p class="label">Date: </p><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
        </tr> 
    <?php endwhile;
    
} else {
    echo '<h4 class="text-left">No records found.</h4>';
}
?>


