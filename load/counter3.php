<?php
session_start();
include '../config/db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}

$available_counters = $_SESSION['counterid'];
$id = $_SESSION['user_id'];
$currentdate = date('Y-m-d');
$query = "SELECT qi.id, qi.queueno, qi.servedby, qi.clientname, qi.branchid, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE servedby = 3 AND status = 'SERVING' AND cashonhandstatus = 'PENDING'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)): ?>
        <thead>

        </thead>
        <tbody> 
            <tr>
                <td class="d-none"><?php echo $row['id']; ?></td>
                <td class="d-none"><?php echo $row['servedby']; ?></td>
                <td class="d-none"><?php echo $row['branchid']; ?></td>
                <td class="strong"><?php echo $row['queueno']; ?></td>
                <td class="strong text-center"><?php echo strtoupper($row['clientname']); ?></td>
                <td class="text-right strong"><?php echo $row['branchname']; ?></td>
            </tr>
        </tbody>
    <?php endwhile;
} else {
    echo '<tr style="pointer-events: none;"><td colspan="9" class="text-center font-weight-bold"><h5>Counter Available</h5></td></tr>';
}

