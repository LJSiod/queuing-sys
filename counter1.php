<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$available_counters = $_SESSION['counterid'];
$id = $_SESSION['user_id'];
$currentdate = date('Y-m-d');
$query = "SELECT qi.id, qi.queueno, qi.servedby, qi.branchid, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE servedby = 1 AND status = 'SERVING' AND cashonhandstatus = 'PENDING'";
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
                <td class="text-right strong"><?php echo $row['branchname']; ?></td>
            </tr>
        </tbody>
    <?php endwhile;
} else {
    echo '';
}
