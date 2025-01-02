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
$query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.activenumber, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE status = 'IN QUEUE'";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)): ?>
    <tr style="cursor: pointer;">
        <td class="d-none"><?php echo $row['id']; ?></td>
        <td class="d-none"><?php echo $row['branchid']; ?></td>
        <td class="d-none"><?php echo str_pad($row['queueno'], 3, '0', STR_PAD_LEFT); ?></td>
        <td><strong><p class="label">Queue no.: </p><?php echo str_pad($row['queueno'], 3, '0', STR_PAD_LEFT); ?></strong></td>
        <td><strong><p class="label">Branch: </p><?php echo $row['branchname']; ?></strong></td>
        <td><p class="label">Type: </p><?php echo $row['type']; ?></td>
        <td><p class="label">Client Name: </p><?php echo $row['clientname']; ?></td>
        <td><p class="label">Loan Amount: </p><?php echo number_format($row['loanamount'], 2, '.', ','); ?></td>
        <td><p class="label">Total Balance: </p><?php echo number_format($row['totalbalance'], 2, '.', ','); ?></td>
        <td><p class="label">Active Number: </p><?php echo $row['activenumber']; ?></td>
        <td><p class="label">Date: </p><?php echo date('F j, Y', strtotime($row['date'])); ?></td>
    </tr>
<?php endwhile; ?>

