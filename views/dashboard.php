<?php
session_start();
include '../config/db.php';
include '../includes/header.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}

$available_counters = $_SESSION['counterid'];
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
    <link href="../assets/css/styles.css" rel="stylesheet">
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
            height: 55vh;
            max-height: 55vh;
            overflow: auto;
        }

        .counter {
            height: 30vh;
            max-height: 30vh;
            overflow: auto;
        }

    </style>

</head>
<body>
    <div class="container-fluid">
        <div class="br-pagebody">
            <?php
            $counternumber = 1;
                echo '<div class="row" id="queuetable">';
                foreach ($available_counters as $counter) {
                    $id = $counter['userid'];
                    $fullname = $counter['fullname'];
                    $colClass = 'col-md';

                    echo '<div class="' . $colClass . '" id="c' . $id . '">
                        <div class="br-section-wrapper counter" id="counter' . $id . '">
                            <div class="sticky-top bg-white" style="z-index: 100;">
                                <div class="d-flex justify-content-between">
                                    <h6 class="font-weight-bold">Counter ' . $counternumber . '</h6>
                                    ' . ($branchid == 8 ? '<span class="small">Running Collection: <strong><span id="c' . $id . 'running"></span></strong></span>' : '') . '
                                </div>
                                <p class="font-weight-bold"></i> ' . $fullname . '</p>
                                <hr>
                            </div>
                            <p class="small">Now Serving: </p>
                            <table class="table table-hover table-sm mt-3" id="queue-table' . $id . '"> 
                            </table>
                        </div>
                    </div>';
                $counternumber++;
                }
                
                echo '</div>';
            ?>
            <div class="br-section-wrapper queue mt-3"
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
                                <th>Date Letter Received</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="small" id="ticket-table-body">
                        </tbody>
                    </table>
                </div>
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
    <?php if ($branch_id == 8) {
    include '../includes/adminjs.php';
    } else {
    include '../includes/branchjs.php';
    } ?>
</body>
</html>

 