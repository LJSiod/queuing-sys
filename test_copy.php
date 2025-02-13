<?php
session_start();
include 'config/db.php';
include 'includes/header.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$branch_id = $_SESSION['branch_id'];
$currentdate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Chivo+Mono|Fira+Sans">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.23/datatables.min.css"/> 
    <link href="assets/css/styles.css" rel="stylesheet">
    <title>Queueing System</title>
    <style>
        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1500px;
            overflow: auto;
        }
        
        .br-section-wrapper {
            background-color: #fff;
            padding: 20px;
            margin-left: 0px;
            margin-right: 0px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
        }

        .recordsdiv {
            height: 90vh;
            max-height: 90vh;
            overflow: auto;
        }

        .top {
            top: -25px;
        }

        .hover {
            transition: all 0.1s ease-in-out;
            cursor: pointer;
        }

        .hover:hover {
            text-shadow: 0px 0px 10px rgba(0, 225, 255, 0.78);
            user-select: none;
        }

        .hover:active {
            text-shadow: 0px 0px 20px rgb(0, 55, 255);
        }

        .hover:not(:hover) {
            color: black;
        }
    </style>
    
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="br-pagebody">
            <div class="br-section-wrapper recordsdiv">
                <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-4">
                <div style="display: flex; align-items: center;">
                    <div>
                        <h5 class="font-weight-bold" style="margin-bottom: -0.5rem;">Records</h5>
                    </div>
                </div>
                </div>
                <table id="records" class="table table-hover table-sm mt-3" style="width: 100%;">
                    <thead class="sticky-top top">
                        <tr title="Click to sort">
                            <th>Queue no.</th>
                            <th>Branch</th>
                            <th>Type</th>
                            <th>Client Name</th>
                            <th>Loan Amount</th>
                            <th>Total Balance</th>
                            <th>On-Hand Cash</th>
                            <th>COH Status</th>
                            <th>Date Letter Received</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody class="small" id="recordtable">
                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center text-danger">
            <span class="font-weight-bold" id="totalonhandcash" style="font-size: 15px;"></span>
        </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/js/font-awesome.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        
    $(document).ready(function() {
    $('#records').DataTable({
        ajax: {
            url: 'loadtest.php',
            type: 'GET',
            dataSrc: 'data'
        },
        pageLength: 17,
        order: [[10, 'desc']],
        columns: [
            { data: 'queueno' },
            { data: 'branchname' },
            { data: 'type' },
            { data: 'clientname' },
            { data: 'loanamount' },
            { data: 'totalbalance' },
            { data: 'cashonhand' },
            { data: 'cashonhandstatus' },
            { data: 'datereceived' },
            { data: 'status' },
            { data: 'date' },
        ]
    });

    $(document).on('contextmenu', '#records tbody tr', function(e) {
            e.preventDefault();
            $('#actiondropdown').remove();

            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            var id = rowData[0]; 
            var menu = $('<div class="dropdown-menu" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>'
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $(document).on('click', function() {
                menu.remove();
            });
        });
    });

</script>
</body>
</html>



