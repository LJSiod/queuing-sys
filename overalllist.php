<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';
include 'header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$branchid = $_GET['branch'];
$query = "SELECT qi.clientname, qi.cashonhand, qi.date, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.branchid = $branchid AND cashonhandstatus = 'RECEIVED' ORDER BY qi.id DESC";
$querytotal = "SELECT SUM(cashonhand) as total FROM queueinfo WHERE cashonhandstatus = 'RECEIVED' AND branchid = '$branchid'";
$resulttotal = mysqli_query($conn, $querytotal);
$rowtotal = mysqli_fetch_assoc($resulttotal);
$result = mysqli_query($conn, $query);
$branchname = mysqli_fetch_assoc($result)['branchname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="styles.css" rel="stylesheet">
    <title>Queueing System</title>
    <style>
        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1000px;
        }

        .br-section-wrapper {
            background-color: #fff;
            padding: 20px;
            margin-left: 0px;
            margin-right: 0px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
        }

        .queue {
            max-width: 100%;
            max-height: 540px;
            overflow: auto;
        }

        .counter {
            max-height: 295px;
            overflow: auto;
        }

        .dont {
            cursor: not-allowed;
        }

        p {
            margin: 5px;
            margin-bottom: 0px;
        }
    </style>
    
</head>
<body>

<div class="br-pagebody">
    <div class="br-section-wrapper">
        <div class="d-flex justify-content-between">
            <h5 class="font-weight-bold">List Preview</h5>
            <h5 class="font-weight-bold"><?php echo $branchname; ?> Branch</h5>
        </div>
            <table class="table table-sm display responsive nowrap small">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Date Paid</th>
                        <th>Amount</th>   
                    <tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td><?php echo strtoupper($row['clientname']); ?></td>
                            <td class="text-right"><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
                            <td class="text-right"><?php echo number_format($row['cashonhand'], 2); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2" class="font-weight-bold">Total</td>
                        <td class="text-right font-weight-bold"><?php echo number_format($rowtotal['total'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
    </div>
</div>
<script>
    $(document).on('contextmenu',function(e) {
        e.preventDefault();
    });
</script>
</body>
</html>