<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';
include 'header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$branchid = $_GET['branch'];
$query = "SELECT qi.id, qi.clientname, qi.cashonhand, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.branchid = $branchid AND date = '$currentdate' AND cashonhandstatus = 'RECEIVED' ORDER BY qi.id DESC";
$querytotal = "SELECT SUM(cashonhand) as total FROM queueinfo WHERE cashonhandstatus = 'RECEIVED' AND branchid = '$branchid' AND date = '$currentdate'";
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

        .list {
            max-width: 100%;
            height: 88vh;
            max-height: 88vh;
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
    <div class="br-section-wrapper list">
        <div class="d-flex justify-content-between">
            <h5 class="font-weight-bold">List Preview</h5>
            <h5 class="font-weight-bold"><?php echo $branchname; ?> Branch</h5>
        </div>
            <table class="table table-hover table-sm display responsive nowrap small">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Amount</th>
                    <tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td class="d-none"><?php echo $row['id']; ?></td>
                            <td><p class="label">Client Name: </p><?php echo strtoupper($row['clientname']); ?></td>
                            <td class="text-right"><p class="label">Amount: </p><?php echo number_format($row['cashonhand'], 2); ?></td>
                        </tr>
                    <?php } ?>
                    <tr style="pointer-events: none;">
                        <td class="font-weight-bold">Total</td>
                        <td class="text-right font-weight-bold"><?php echo number_format($rowtotal['total'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
    </div>
    <div class="d-flex mt-1 justify-content-end">
        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="window.history.back();">Close</button>
    </div>
</div>
<script>
$(document).ready(function() {
    $(document).on('click',function(e) {
        $('.removedrop').remove();
    })
    $(document).on('contextmenu', function(e) {
        e.preventDefault();
    });
    $(document).on('contextmenu', 'tr', function(e) {
        e.preventDefault();
        $('.removedrop').remove();

        var rowData = $(this).children('td').map(function() {
            return $(this).text();
        }).get();
        console.log(rowData);
        var id = rowData[0]; 
        var menu = $('<div class="dropdown-menu removedrop" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                    + '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>'
                    + '</div>').appendTo('body');
        menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});
    });
});
</script>
</body>
</html>