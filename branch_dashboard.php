<?php
session_start();
include 'db.php';
include 'header.php';
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
    <link href="styles.css" rel="stylesheet">
    <title>Queueing System</title>
    <style>
        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1300px;
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

    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="br-pagebody">
            <div class="row" id="queuetable">
            <div class="col-md-6">
            <div class="br-section-wrapper counter mt-3">
                <div class="sticky-top bg-white" style="z-index: 100;">
                    <h6 class="font-weight-bold">Counter 1</h6>
                    <p class="font-weight-bold"> Kim Sabalo </p>
                    <!-- <i class="fa fa-circle" id="active1" style="font-size:12px;"></i> -->
                        <hr>
                    <p class="small">Now Serving: </p>
                </div>
                <table class="table table-hover table-sm mt-3" id="queue-table1"> 
                </table>
            </div>
            </div>
            <div class="col-md-6">
            <div class="br-section-wrapper counter mt-3">
                <div class="sticky-top bg-white" style="z-index: 100;">
                        <h6 class="font-weight-bold">Counter 2</h6>
                    <p class="font-weight-bold"> Mae Demetais</p>
                    <!-- <i class="fa fa-circle" id="active2" style="font-size:12px;"></i> -->
                        <hr>
                    <p class="small">Now Serving: </p>
                </div>
                <table class="table table-hover table-sm mt-3" id="queue-table2"> 
                </table>
            </div>
            </div>
            </div>
        <div class="br-section-wrapper queue mt-3">
        <h6><strong>Queues</strong></h6>
        <table id="ticket-table" class="table table-hover table-sm mt-3" style="width: 100%;">
            <thead>
                <tr>
                    <th>Queue no.</th>
                    <th>Branch</th>
                    <th>Type</th>
                    <th>Client Name</th>
                    <th>Loan Amount</th>
                    <th>Total Balance</th>
                    <th>Active Number</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody class="small" id="ticket-table-body">
            </tbody>
        </table>
        </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        loadTickets();
        loadQ1();
        loadQ2();
        setInterval(function() { loadTickets(); loadQ1(); loadQ2(); }, 5000);

        function loadQ1() {
            $.ajax({
                url: 'queue1.php',
                method: 'GET',
                success: function(data) {
                    $('#queue-table1').html(data);
                }
            });
        }

        function loadQ2() {
            $.ajax({
                url: 'queue2.php',
                method: 'GET',
                success: function(data) {
                    $('#queue-table2').html(data);
                }
            });
        }

        function loadTickets() {
            $.ajax({
                url: 'loadqueue.php',
                method: 'GET',
                success: function(data) {
                    $('#ticket-table-body').html(data);
                }
            });
        }

        $(document).on('contextmenu', function(e) {
            e.preventDefault();

        });

        $(document).on('contextmenu', '#queuetable table tbody tr',  function(e) {
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var branchid = rowData[2];
            var menu = $('<div class="dropdown-menu small removedrop" style="display:block; position:absolute; z-index:1000;">'
                        + (branchid == '<?php echo $_SESSION['branch_id']; ?>' ? '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>' : '<a class="dropdown-item small disabled" href="#" id="receive"><i class="fa fa-lock text-secondary" aria-hidden="true"></i> <span class="ml-2">Restricted</span></a>')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

                $(document).on('click', function() {
                menu.remove();
            });
        });

        $(document).on('contextmenu', '#ticket-table tbody tr',  function(e) {
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var branchid = rowData[1];
            var menu = $('<div class="dropdown-menu small removedrop" style="display:block; position:absolute; z-index:1000;">'
                        + (branchid == '<?php echo $_SESSION['branch_id']; ?>' ? '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>' : '<a class="dropdown-item small disabled" href="#" id="receive"><i class="fa fa-lock text-secondary" aria-hidden="true"></i> <span class="ml-2">Restricted</span></a>')
                        // + (branchid == '<?php echo $_SESSION['branch_id']; ?>' ? '<a class="dropdown-item small" href="edit.php?id=' + id + '" id="edit"><i class="fa fa-edit text-primary" aria-hidden="true"></i> Edit</a>' : '')
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

