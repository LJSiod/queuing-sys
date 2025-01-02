<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$query1 = "SELECT qi.*, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE servedby = 1 AND status = 'SERVED' AND cashonhandstatus = 'PENDING'";
$query2 = "SELECT qi.*, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE servedby = 3 AND status = 'SERVED' AND cashonhandstatus = 'PENDING'";

$result1 = mysqli_query($conn, $query1);
$result2 = mysqli_query($conn, $query2);

$data = array(
    'queue1' => '',
    'queue2' => '',
);

while ($row = mysqli_fetch_assoc($result1)) {
    $data['queue1'] .= '<thead></thead>
    <tbody>
        <tr>
            <td class="strong">
                <span class="d-none">' . $row['ql'] . '</span>
                <span class="d-none">' . $row['servedby'] . '</span>
                <span>' . str_pad($row['queueno'], 3, '0', STR_PAD_LEFT) . '</span>
                <span style="float:right;">' . $row['branchname'] . '</span>
            </td>
        </tr>
    </tbody>';
}

while ($row = mysqli_fetch_assoc($result2)) {
    $data['queue2'] .= '<thead></thead>
    <tbody>
        <tr>
            <td class="strong">
                <span class="d-none">' . $row['ql'] . '</span>
                <span class="d-none">' . $row['servedby'] . '</span>
                <span>' . str_pad($row['queueno'], 3, '0', STR_PAD_LEFT) . '</span>
                <span style="float:right;">' . $row['branchname'] . '</span>
            </td>
        </tr>
    </tbody>';
}


echo json_encode($data);
