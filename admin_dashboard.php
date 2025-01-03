<?php
session_start();

include 'db.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$currentdate = date('Y-m-d');
$id = $_SESSION['user_id'];
$branch_id = $_SESSION['branch_id'];
include 'header.php';
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
    <div class="container-fluid mt-3">
        <div class="br-pagebody">
            <div class="row" id="queuetable">
            <div class="col-md-6">
            <div class="br-section-wrapper bgwhite counter mt-3" id="counter1">
                <div class="sticky-top bg-white" style="z-index: 100;">
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-bold">Counter 1</h6>
                        <span class="small">Running Collection: <strong><span id="c1running"></span></strong></span>
                    </div>
                    <p class="font-weight-bold"></i> Kim Sabalo </p>
                    <!-- <i class="fa fa-circle" id="active1" style="font-size:12px;"> -->
                        <hr>
                    <p class="small">Now Serving: </p>
                </div>
                <table class="table table-hover table-sm mt-3" id="queue-table1"> 
                </table>
            </div>
            </div>
            <div class="col-md-6">
            <div class="br-section-wrapper counter mt-3" id="counter2">
                <div class="sticky-top bg-white" style="z-index: 100;">
                    <div class="d-flex justify-content-between">
                        <h6 class="font-weight-bold">Counter 2</h6>
                        <span class="small">Running Collection: <strong><span id="c2running"></span></strong></span>
                    </div>
                    <p class="font-weight-bold"></i> Mae Demetais</p>
                    <!-- <i class="fa fa-circle" id="active2" style="font-size:12px;"> -->
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

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="previewContent"></div>
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

        $(document).on('contextmenu', '#queuetable table tbody tr', function(e) {
            $('.removedrop').remove();
            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var served = rowData[1];
            var branchid = rowData[2];
            var menu = $('<div class="dropdown-menu small removedrop" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="#" id="receive"><i class="fa fa-check-circle text-success" aria-hidden="true"></i> <span>Accomplished: Received</span></a>' : '<a class="dropdown-item small disabled" href="#" id="receive"><i class="fa fa-lock text-secondary dont" aria-hidden="true"></i> <span class="ml-2">Restricted</span></a>')
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="#" id="decline"><i class="fa fa-times-circle text-danger" aria-hidden="true"></i> Accomplished: Declined</a>' : '')
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>' : '')
                        + (served == '<?php echo $_SESSION['user_id']; ?>' ? '<a class="dropdown-item small" href="#" id="return"><i class="fa fa-undo text-primary" aria-hidden="true"></i> Return to Queue</a>' : '')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $('#receive').on('click', function() {
              swal({
                title: "Accomplished: Received",
                text: "Input Note",
                icon: "info",
                content: {
                  element: "textarea",
                  attributes: {
                    placeholder: "Enter Note",
                    rows: 5,
                    id: "note-textarea"
                  },
                },
                buttons: {
                  cancel: "Cancel",
                  confirm: "Confirm"
                }
              }).then((value) => {
                if (value) {
                  var note = $("#note-textarea").val();
                  $.ajax({
                    url: 'receive.php',
                    method: 'POST',
                    data: {id: id, note: note, branchid: branchid},
                    success: function() {
                      swal({
                        title: "Received",
                        text: "Payment Received",
                        icon: "success",
                        buttons: false,
                        timer: 1500
                      }).then(function() {
                        location.reload();
                      });
                    }
                  });
                }
              });
            });

            $('#decline').on('click', function() {
              swal({
                title: "Accomplished: Declined",
                text: "Input Note",
                icon: "info",
                content: {
                  element: "textarea",
                  attributes: {
                    placeholder: "Enter Note",
                    rows: 5,
                    id: "note-textarea1"
                  },
                },
                buttons: {
                  cancel: "Cancel",
                  confirm: "Confirm"
                }
              }).then((value) => {
                if (value) {
                  var note = $("#note-textarea1").val();
                  $.ajax({
                    url: 'decline.php',
                    method: 'POST',
                    data: {id: id, note: note},
                    success: function() {
                      swal({
                        title: "Declined",
                        text: "Payment Declined",
                        icon: "success",
                        buttons: false,
                        timer: 1500
                      }).then(function() {
                        location.reload();
                      });
                    }
                  });
                }
              });
            });

            $('#return').on('click', function() {
                $.ajax({
                    url: 'return.php',
                    method: 'POST',
                    data: {id: id},
                    success: function(response) {
                        swal({
                            title: 'Success',
                            text: 'Returned to Queue!',
                            icon: 'success',
                            timer: 1500,
                            buttons: false
                        });
                        loadTickets(); 
                        loadQ1();
                        loadQ2();
                    }
                });
            });
                $(document).on('click', function() {
                menu.remove();
            });
        });

        $(document).on('contextmenu', '#ticket-table tbody tr', function(e) {
            e.preventDefault();
            $('.removedrop').remove();

            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var queueno = rowData[2];
            var menu = $('<div class="dropdown-menu small removedrop" id="queuedropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<span class="dropdown-item small">No.: <b class="small">' + queueno + '</b></span>'
                        + '<div class="dropdown-divider"></div>'
                        + '<a class="dropdown-item small serve" href="#"><i class="fa fa-check-circle text-success" aria-hidden="true"></i> Serve</a>'
                        + '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>'
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $('.serve').on('click', function() {
                $.ajax({
                    url: 'serve.php',
                    method: 'POST',
                    data: {id: id},
                    success: function(response) {
                        swal({
                            title: 'Success',
                            text: 'Serving Client!',
                            icon: 'success',
                            timer: 1500,
                            buttons: false
                        });
                        loadTickets(); 
                        loadQ1();
                        loadQ2();
                    }
                });
                menu.remove();
            });

            $(document).on('click', function() {
                menu.remove();
            });
        });
        $(document).on('change', '#ticket-table tbody tr', function(e) {    
            e.preventDefault();
            $('#alert').removeClass('d-none');
            setTimeout(function() {
                $('#alert').addClass('d-none');
            }, 1500);
        })
    });

</script>
</body>
</html>

 