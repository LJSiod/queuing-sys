<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$query = "SELECT DATE(date) AS date, SUM(cashonhand) AS totalperday, COUNT(cashonhand) AS paidperday 
FROM queueinfo WHERE cashonhandstatus = 'RECEIVED' 
GROUP BY DATE(date) ORDER BY DATE(date) DESC LIMIT 30";
$result = mysqli_query($conn, $query);
?>

    <thead>
        <tr style="pointer-events: none;">
            <th class="font-weight-bold small">Date</th>
            <th class="font-weight-bold text-right small">Amount Collected</th>
            <th class="font-weight-bold text-right small">No. of Accounts Settled</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr class="<?php if ($row['date'] == $currentdate) { echo 'today'; } ?>">
                <td class="d-none"><?php echo $row['date']; ?></td>
                <td class="small"><p class="label">Date: </p><?php if ($row['date'] == $currentdate) { echo 'Today'; } else { echo date('M d, Y', strtotime($row['date'])); } ?></td>
                <td class="text-right small"><p class="label">Amount: </p><?php echo number_format($row['totalperday'], 2); ?></td>
                <td class="text-right small"><p class="label">Paid: </p><?php echo $row['paidperday']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
