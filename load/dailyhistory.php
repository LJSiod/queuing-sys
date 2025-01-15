<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$query1 = "SELECT SUM(qi.cashonhand) AS total, COUNT(qi.id) AS paid FROM queueinfo qi WHERE qi.cashonhandstatus = 'RECEIVED'";
$result1 = mysqli_query($conn, $query1);
$row1 = mysqli_fetch_assoc($result1);

$query = "SELECT DATE(date) AS date, SUM(cashonhand) AS totalperday, COUNT(cashonhand) AS paidperday 
FROM queueinfo WHERE cashonhandstatus = 'RECEIVED' 
GROUP BY DATE(date) ORDER BY DATE(date) DESC";
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
