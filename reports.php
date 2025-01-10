<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';
include 'header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Chivo+Mono|Nunito+Sans">
    <link href="styles.css" rel="stylesheet">
    <style>

        .mt1 {
            margin-top: 50px;
        }

        .today {
            background-color:rgb(152, 251, 152);
        }

        #max {
            max-height: 66.5vh;
            height: 60vh;
            overflow: auto;
        }

        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1800px;

        }

        .br-section-wrapper {
            background-color: #fff;
            padding: 20px;
            margin-left: 0px;
            margin-right: 0px;
            margin-bottom: 10px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
            max-height: 90vh;
            overflow: auto;
        }

        .fileThumbnail {
          width: 100%;
          height: 100%;
          max-width: 70%;
          max-height: 350px;
          margin-bottom: 10px;
          border: 1px solid #e5e5e5;
        }
        
        .dragover {
            background-color: #ccc;
            cursor: move;
        }

        @media (max-width: 768px) {
            .text-right {
                text-align: left !important;
            }
        }

    </style>
</head>
<body>
    <div class="br-pagebody">
        <div class="row">
            <!-- <div class="col-sm">
                <div class="br-section-wrapper">
                    <h5 class="font-weight-bold">Daily Summary</h5>
                    <table class="table table-hover table-sm" id="queue-table1"> 
                        <?php include 'loaddaily.php' ?>
                    </table>
                </div>
            </div> -->
            <div class="col-sm-8">
                <div class="br-section-wrapper">
                    <h5 class="font-weight-bold">Real Time Summary</h5>
                    <table class="table table-hover table-sm" id="queue-table2"> 
                        <?php include 'loadoverall.php' ?>
                    </table>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="br-section-wrapper" id="max">
                    <h5 class="font-weight-bold">Daily History</h5>
                    <table class="table table-hover table-sm" id="history"> 
                        <?php include 'dailyhistory.php' ?>
                    </table>
                </div>
                <div class="br-section-wrapper">
                    <div class="d-flex justify-content-between">
                        <h5 class="font-weight-bold">Totals</h5>
                        <span class="small text-primary"><i><u>Overall from December 10, 2024</u></i></span>
                    </div>
                    <table class="table table-sm" id="totals"> 
                        <?php include 'loadtotals.php' ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

        $(document).on('contextmenu', function(e) {
            e.preventDefault();
        });

        $(document).on('click', function(e) {
            $('.removedrop').remove();
        });

        $(document).ready(function() {
            loadoverall();
            loadtotals();
            loadhistory();
            setInterval(() => {
                loadoverall();
                loadtotals();
                loadhistory();
            }, 5000);


        $('#queue-table2').on('contextmenu', 'tr', function(e) {
            e.preventDefault();
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            var branchid = rowData[0];
            var paid = rowData[2];
            console.log(branchid, paid);
            var menu = $('<div class="dropdown-menu small removedrop" id="queuedropdown" style="display:block; position:absolute; z-index:1000;">'
                        + (paid != 0 ? '<a class="dropdown-item small" href="overalllist.php?branch=' + branchid + '" id="list"><i class="fa fa-calendar text-info" aria-hidden="true"></i> Preview Overall</a>' : '<span class="dropdown-item small text-muted">No Collection</span>')
                        + (paid != 0 ? '<a class="dropdown-item small" href="dailylist.php?branch=' + branchid + '" id="list"><i class="fa fa-calendar-check-o text-info" aria-hidden="true"></i> Preview Daily</a>' : '<span class="dropdown-item small text-muted">No Collection</span>')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

        });

        $('#history').on('contextmenu', 'tr', function(e) {
            e.preventDefault();
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            var date = rowData[0];
            console.log(date);
            var menu = $('<div class="dropdown-menu small removedrop" id="queuedropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<a class="dropdown-item small" href="history.php?&date=' + date + '" id="list"><i class="fa fa-list text-info" aria-hidden="true"></i> Preview List</a>'
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});
            
        });

            function loadoverall() {
                $.ajax({
                    url: 'loadoverall.php',
                    method: 'GET',
                    success: function(data) {
                        $('#queue-table2').html(data);
                    }
                });
            }

            function loadtotals() {
                $.ajax({
                    url: 'loadtotals.php',
                    method: 'GET',
                    success: function(data) {
                        $('#totals').html(data);
                    }
                });
            }

            function loadhistory() {
                $.ajax({
                    url: 'dailyhistory.php',
                    method: 'GET',
                    success: function(data) {
                        $('#history').html(data);
                    }
                });
            }

        })
    </script>
    </body>
</html>





