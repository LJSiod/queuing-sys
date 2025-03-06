<?php
session_start();
include '../config/db.php';
include '../includes/header.php';
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

        .recordsdiv {
            height: 88vh;
            max-height: 88vh;
            overflow: auto;
        }

        .top {
            top: -25px;
        }

        #recordtable tr:hover {
            background-color:rgb(234, 232, 232);
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
                        <h5 class="font-weight-bold" style="margin-bottom: -5rem;">Records</h5>
                    </div>
                </div>
                </div>
                <table id="records" class="table table-sm" style="width: 100%;">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/js/font-awesome.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/sc-2.4.3/datatables.min.js" integrity="sha384-1zOgQnerHMsipDKtinJHWvxGKD9pY4KrEMQ4zNgZ946DseuYh0asCewEBafsiuEt" crossorigin="anonymous"></script>
    <script>
        
    $(document).ready(function() {
    var table = $('#records').DataTable({
      ajax: {
        url: '../load/loadrecords.php',
        type: 'GET',
        dataSrc: 'data'
      },
      layout: {
        topStart: false,
        bottomEnd: false,
      },
      order: [
        [10, 'desc'],
        [0, 'desc']
      ],
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
      ],
      language: {
        searchPlaceholder: 'Search',
        search: '',
        loadingRecords: 'Loading...'
      },
      deferRender: true,
      scroller: true,
      scrollY: "64vh",
      initComplete: function(settings, json) {
        $('.dt-layout-row').css({
          'font-size': '17px',
          'font-weight': 'bold'
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
    $(document).on('contextmenu', '#records tbody tr', function(e) {
            e.preventDefault();
            $('#actiondropdown').remove();

            var rowData = table.row($(this)).data();
            var id = rowData.id; 
            var menu = $('<div class="dropdown-menu" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>'
                        + (<?= $branch_id ?> == 8 ? '<a class="dropdown-item small" href="#" id="receive"><i class="fa fa-check text-success" aria-hidden="true"></i> Mark as Received</a>' : '')
                        + (<?= $branch_id ?> == 8 ? '<a class="dropdown-item small" href="#" id="decline"><i class="fa fa-times text-danger" aria-hidden="true"></i> Mark as Declined</a>' : '')
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $('#receive').on('click', function() {
                $.ajax({
                    url: '../actions.php',
                    type: 'POST',
                    data: {
                        action: 'updatereceived',
                        id: id
                    },
                    success: function(response) {
                            Swal.fire({
                              title: 'Success!',
                              text: 'Marked as Received!',
                              icon: 'success',
                              showConfirmButton: false,
                              timer: 1500

                            }); 
                            table.ajax.reload(null, false);
                            menu.remove();
                        }
                    })
                })
            
            $('#decline').on('click', function() {
                $.ajax({
                    url: '../actions.php',
                    type: 'POST',
                    data: {
                        action: 'updatedeclined',
                        id: id
                    },
                    success: function(response) {
                            Swal.fire({
                              title: 'Success!',
                              text: 'Marked as Declined!',
                              icon: 'success',
                              showConfirmButton: false,
                              timer: 1500
                            });
                            table.ajax.reload(null, false);
                            menu.remove();
                        }
                    })
                })


            $(document).on('click', function() {
                menu.remove();
            });
        });
    });

</script>
</body>
</html>



