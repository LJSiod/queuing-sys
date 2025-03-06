<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}
$currentdate = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Chivo+Mono|Nunito+Sans">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../assets/css/styles.css" rel="stylesheet">
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
            max-height: 88vh;
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
            <div class="col-sm-6">
                <div class="br-section-wrapper">
                    <h5 class="font-weight-bold">Daily History</h5>
                    <table class="table table-hover table-sm" id="history"> 
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2" style="width: 13%;">Date</th>
                                    <th colspan="4">Total Accounts</th>
                                    <th colspan="4">Total Amount Collected</th>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold text-right small">Billing Statement</th>
                                    <th class="font-weight-bold text-right small">Demand Letter</th>
                                    <th class="font-weight-bold text-right small">Preliminary Notice</th>
                                    <th class="font-weight-bold text-right small">Total</th>
                                    <th class="font-weight-bold text-right small">Billing Statement</th>
                                    <th class="font-weight-bold text-right small">Demand Letter</th>
                                    <th class="font-weight-bold text-right small">Preliminary Notice</th>
                                    <th class="font-weight-bold text-right small">Total</th>
                                </tr>
                            </thead>
                            <tbody class="small">

                            </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="br-section-wrapper">
                    <div class="d-flex">
                        <h5 class="font-weight-bold" id="viewtype">Per Branch Summary</h5>
                        <div class="form-group form-inline ml-auto">
                            <span class="small font-weight-bold mr-1">View Mode:</span>
                            <select class="form-control form-control-sm" id="view">
                                <option>Summary</option>
                                <option>Detailed</option>
                            </select>
                        </div>
                    </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="form-group form-inline mr-2" id="selectdiv">
                                <div class="form-check">
                                    <label class="form-check-label small font-weight-bold mr-1" for="selectall">Select All</label>
                                    <input class="form-check-input" type="checkbox" id="selectall" checked>
                                </div>
                            </div>
                            <div class="form-group form-inline mr-2" id="startdiv">
                                <label class="small font-weight-bold mr-1" for="startDate">Start Date:</label>
                                <input type="date" class="form-control form-control-sm" id="startdate" disabled>
                            </div>
                            <div class="form-group form-inline mr-2" id="enddiv">
                                <label class="small font-weight-bold mr-1" for="endDate">End Date:</label>
                                <input type="date" class="form-control form-control-sm" id="enddate" disabled>
                            </div>
                        </div>
                    <table class="table table-hover table-sm" id="queue-table2"> 
                        
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/sc-2.4.3/datatables.min.js" integrity="sha384-1zOgQnerHMsipDKtinJHWvxGKD9pY4KrEMQ4zNgZ946DseuYh0asCewEBafsiuEt" crossorigin="anonymous"></script>
    <script>

        $(document).on('contextmenu', function(e) {
            e.preventDefault();
        });

        $(document).on('click', function(e) {
            $('.removedrop').remove();
        });

        var url = '../load/loadsummary.php';
        var filter = $('#selectdiv, #startdiv, #enddiv');
        var view = $('#view').val();

        $('#view').on('change', function() {
            view = $(this).val();
            if (view == 'Summary') {
                url = '../load/loadsummary.php';
                filter.hide();
                $('#viewtype').html('Per Branch Summary');
                
            } else {
                url = '../load/loaddetailed.php';
                filter.show();
                $('#viewtype').html('Per Branch Detailed');
            }

            loadoverall();
        });

        $('#selectall').on('click', function() {
            if ($(this).is(':checked')) {
                $('#startdate').prop('disabled', true);
                $('#startdate').val(null);
                $('#enddate').prop('disabled', true);
                $('#enddate').val(null);
                loadoverall();
            } else {
                $('#startdate').prop('disabled', false);
                $('#startdate').val('2024-12-10');
                $('#enddate').prop('disabled', false);
                $('#enddate').val('<?php echo $currentdate; ?>');
            }
        });

        $('#startdate, #enddate').on('change', function() {
            var startdate = $('#startdate').val();
            var enddate = $('#enddate').val();
            var betweenquery = "BETWEEN '" + startdate + "' AND '" + enddate + "'";
            loadoverall(betweenquery);
        });

            function loadoverall(betweenquery, sortby) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        betweenquery: betweenquery,
                        sortby: sortby
                    },
                    success: function(response) {
                        $('#queue-table2').html(response);
                    }
                });
            }

        $(document).on('click', '#queue-table2 thead th', function(e) {
            e.preventDefault();
            $('#actiondropdown').remove();

            var thvalue = $(this).attr('id');
            var startdate = $('#startdate').val();
            var enddate = $('#enddate').val();
            var betweenquery = (startdate && enddate) ? "BETWEEN '" + startdate + "' AND '" + enddate + "'" : null;
            loadoverall(betweenquery, thvalue);
        });

        $(document).ready(function() {
            filter.hide();
            loadoverall();
            setInterval(() => {
            }, 5000);

        $('#queue-table2').on('contextmenu', 'tbody tr', function(e) {
            e.preventDefault();
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            var branchid = rowData[0];
            var paid = rowData[2];
            console.log(view);
            var menu = $('<div class="dropdown-menu small removedrop" id="queuedropdown" style="display:block; position:absolute; z-index:1000;">'
                        + (paid != 0 ? '<a class="dropdown-item small" href="overalllist.php?branch=' + branchid + '" id="list"><i class="fa fa-calendar text-info" aria-hidden="true"></i> Preview Overall</a>' : '<span class="dropdown-item small text-muted">No Collection</span>')
                        + (paid != 0 ? '<a class="dropdown-item small" href="dailylist.php?branch=' + branchid + '" id="list"><i class="fa fa-calendar-check-o text-info" aria-hidden="true"></i> Preview Daily</a>' : '<span class="dropdown-item small text-muted">No Collection</span>')
                        + '</div>').appendTo('body');
            if (view == 'Summary') {
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});
            } else {
                menu.css({display: 'none'});
            }

        });

        $('#history').on('contextmenu', 'tr', function(e) {
            e.preventDefault();
            $('.removedrop').remove();
            var rowData = table.row($(this)).data();
            var date = rowData.date;
            var menu = $('<div class="dropdown-menu small removedrop" id="queuedropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<a class="dropdown-item small" href="history.php?&date=' + date + '" id="list"><i class="fa fa-list text-info" aria-hidden="true"></i> Preview List</a>'
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});
            
        });
            var table = $('#history').DataTable({
                ajax: {
                    url: '../load/dailyhistory.php',
                    type: 'GET',
                    dataSrc: 'data'
                },
                layout: {
                    topStart: false,
                    topEnd: false,
                    bottomEnd: false,
                },
                order: [
                    [0]
                ],
                rowCallback: function(row, data, index) {
                    if (data.dateformatted === 'Today') {
                        $(row).css('background-color', '#98fb98');
                    }
                },
                columns: [
                    { data: 'dateformatted' },
                    { data: 'tas_bs' },
                    { data: 'tas_dl' },
                    { data: 'tas_pn' },
                    { data: 'totaltas' },
                    { data: 'ac_bs' },
                    { data: 'ac_dl' },
                    { data: 'ac_pn' },
                    { data: 'totalac' },
                ],
                language: {
                    loadingRecords: 'Loading...',
                },
                deferRender: true,
                scroller: true,
                scrollY: '65vh',
                initComplete: function(settings, json) {
            $('.dt-layout-row').css({
              'font-size': '15px',
              'font-weight': 'bold'
            });
            }
            });
        })
    </script>
    </body>
</html>
