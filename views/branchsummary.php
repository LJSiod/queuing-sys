<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
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
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet"
        integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <style>
        .mt1 {
            margin-top: 50px;
        }

        .today {
            background-color: #98fb98;
        }

        #max {
            max-height: 21vh;
            height: 21vh;
            overflow: auto;
        }

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
            margin-bottom: 10px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
            max-height: 90vh;
            overflow: auto;
        }
    </style>
</head>

<body>
    <div class="br-pagebody">
        <div class="row">
            <div class="col-md-9">
                <div class="br-section-wrapper">
                    <h5 class="font-weight-bold">History</h5>
                    <table class="table table-sm small" id="queue-table2">
                        <thead>
                            <tr class="text-center" title="Click to Sort">
                                <th rowspan="3" style="width: 8%;" id="date">Date</th>
                            </tr>
                            <tr style="pointer-events: none;" class="text-center">
                                <th id="bsdaily" colspan="4">Amount Collected</th>
                                <th id="dldaily" colspan="4">Total Number of Accounts Settled</th>
                            </tr>
                            <tr title="Click to Sort">
                                <th id="ac_bs">Billing Statement</th>
                                <th id="ac_dl">Demand Letter</th>
                                <th id="ac_pn">Preliminary Notice</th>
                                <th id="totalac">Total Amount</th>
                                <th id="noa_bs">Billing Statement</th>
                                <th id="noa_dl">Demand Letter</th>
                                <th id="noa_pn">Preliminary Notice</th>
                                <th id="totalnoa">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="br-section-wrapper" id="max">
                    <h5 class="font-weight-bold">Totals</h5>
                    <hr>
                    <div id="totals"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/sc-2.4.3/datatables.min.js"
        integrity="sha384-1zOgQnerHMsipDKtinJHWvxGKD9pY4KrEMQ4zNgZ946DseuYh0asCewEBafsiuEt"
        crossorigin="anonymous"></script>
    <script>

        $(document).ready(function () {
            var table = $('#queue-table2').DataTable({
                ajax: {
                    url: '../load/loadbranchsummary.php',
                    type: 'GET',
                    dataSrc: 'data'
                },
                layout: {
                    topStart: false,
                    bottomEnd: false,
                },
                order: [[0, 'desc']],
                rowCallback: function (row, data, index) {
                    if (data.date === '<?php echo date('Y-m-d'); ?>') {
                        $(row).css('background-color', '#98fb98');
                    }
                },
                columns: [
                    { data: 'date' },
                    { data: 'ac_bs' },
                    { data: 'ac_dl' },
                    { data: 'ac_pn' },
                    { data: 'totalac' },
                    { data: 'bs_noatas' },
                    { data: 'dl_noatas' },
                    { data: 'pn_noatas' },
                    { data: 'totalnoatas' },
                ],
                language: {
                    searchPlaceholder: 'Search',
                    search: '',
                    loadingRecords: 'Loading...'
                },
                deferRender: true,
                scroller: true,
                scrollY: "60vh",
                initComplete: function (settings, json) {
                    $('.dt-layout-row').css({
                        'font-size': '15px',
                        'font-weight': 'bold'
                    });
                }
            });

            $.ajax({
                url: '../load/loadtotals.php',
                method: 'GET',
                success: function (data) {
                    $('#totals').html(data);
                }
            })

            setInterval(function () {
                table.ajax.reload(null, false);
            }, 15000);

            $(document).on('contextmenu', function (e) {
                e.preventDefault();
            });
        });
    </script>
</body>

</html>