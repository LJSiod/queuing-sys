<?php
session_start();
include '../config/db.php';
include '../includes/header.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
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
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <title>Queueing System</title>
    <style>
        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1500px;
        }

        .br-section-wrapper {
            background-color: #fff;
            padding: 20px;
            margin-left: 0px;
            margin-right: 0px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
        }

        .notediv {
            height: 90vh;
            max-height: 90vh;
            overflow: auto;
        }

        .top {
            top: -25px;
        }

    </style>
    
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="br-pagebody">
            <div class="br-section-wrapper notediv">
                <div>
                    <h5 class="font-weight-bold" style="margin-bottom: -0.5rem;">Notes</h5>
                </div>     
                <table id="notes" class="table table-hover table-sm" style="width: 100%;">
                    <thead class="sticky-top top">
                        <tr>
                            <th>No.</th>
                            <th>Branch</th>
                            <th>Client Name</th>
                            <th>Remarks</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th style="width: 7%;">Date</th>
                        </tr>
                    </thead>
                    <tbody class="small" id="notetable">
                    </tbody>
                </table>           
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/js/font-awesome.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/sc-2.4.3/datatables.min.js" integrity="sha384-1zOgQnerHMsipDKtinJHWvxGKD9pY4KrEMQ4zNgZ946DseuYh0asCewEBafsiuEt" crossorigin="anonymous"></script>
    <script>

        $(document).ready(function() {
        var table = $('#notes').DataTable({
          ajax: {
            url: '../load/loadnoterecords.php',
            type: 'GET',
            dataSrc: 'data'
          },
          layout: {
            topStart: false,
            bottomEnd: false,
          },
          order: [
            [6, 'desc'],
            [0, 'desc']
        ],
          columns: [
            { data: 'queueno' },
            { data: 'branchname' },
            { data: 'clientname' },
            { data: 'remarks' },
            { data: 'note' },
            { data: 'cashonhandstatus' },
            { data: 'date' },
          ],
          language: {
            searchPlaceholder: 'Search',
            search: '',
            loadingRecords: 'Loading...',
          },
          deferRender: true,
          scroller: true,
          scrollY: "70vh",
          initComplete: function(settings, json) {
            $('.dt-layout-row').css({
              'font-size': '15px',
              'font-weight': 'bold'
            });
            $('#notes thead th').css({
              'font-size': '13px',
            });
          }
        });

        if ($(window).width() >= 768) {
          setInterval(function() {
            table.ajax.reload(null, false);
          }, 30000);
        }

        $(document).on('contextmenu', function(e) {
        e.preventDefault();
        });

        $(document).on('contextmenu', '#notes tbody tr', function(e) {
            e.preventDefault();
            $('#actiondropdown').remove();

            var rowData = table.row($(this)).data();
            var id = rowData.idpreview; 
            console.log(rowData.idpreview);
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
