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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Chivo+Mono|Fira+Sans">
    <link href="styles.css" rel="stylesheet">
    <title>Queueing System</title>
    <style>
        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1500px;
            max-height: 87vh;
            overflow: auto;
        }

        .br-section-wrapper {
            background-color: #fff;
            padding: 20px;
            margin-left: 0px;
            margin-right: 0px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
        }
    </style>
    
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-4">
                <div style="display: flex; align-items: center;">
                    <div>
                        <h5 class="font-weight-bold" style="margin-bottom: -0.5rem;">Notes</h5>
                    </div>
                </div>
                <div>
                    <input type="date" class="form-control form-control-sm" id="filterdate" name="filterdate" value="<?php echo $currentdate; ?>">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectalldate">
                        <label class="form-check-label small" for="selectalldate">
                            Select All Date
                        </label>
                    </div>               
                </div>
                </div>
                <table id="notes" class="table table-hover table-sm mt-3" style="width: 100%;">
                    <thead class="sticky-top">
                        <tr>
                            <th>No.</th>
                            <th>Branch</th>
                            <th style="width: 200px;">Client Name</th>
                            <th>Remarks</th>
                            <th style="width: 300px;">Note</th>
                            <th>Status</th>
                            <th style="width: 80px;">Date</th>
                        </tr>
                    </thead>
                    <tbody class="small" id="notetable">
                    </tbody>
                </table>
                <div id="loading" class="loader mx-auto">
                    <div class="bar1"></div>
                    <div class="bar2"></div>
                    <div class="bar3"></div>
                    <div class="bar4"></div>
                    <div class="bar5"></div>
                    <div class="bar6"></div>
                    <div class="bar7"></div>
                    <div class="bar8"></div>
                    <div class="bar9"></div>
                    <div class="bar10"></div>
                    <div class="bar11"></div>
                    <div class="bar12"></div>
                </div>            
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/js/font-awesome.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).on('ajaxStart', function () {
            $('#loading').show();
        }).on('ajaxStop', function () {
            $('#loading').hide();
        });

        $(document).ready(function() {
        $(document).on('contextmenu',function(e) {
            e.preventDefault();
        });
        $(document).on('contextmenu', '#notes tbody tr', function(e) {
            e.preventDefault();
            $('#actiondropdown').remove();

            var rowData = $(this).children('td').map(function() {
                return $(this).text();
            }).get();
            console.log(rowData);
            var id = rowData[0]; 
            var menu = $('<div class="dropdown-menu" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                        + '<a class="dropdown-item small" href="preview.php?id=' + id + '" id="preview"><i class="fa fa-eye text-info" aria-hidden="true"></i> Preview</a>'
                        + '<a class="dropdown-item small" href="#" onclick="location.reload();"><i class="fa fa-refresh text-success" aria-hidden="true"></i> Refresh</a>'
                        + '</div>').appendTo('body');
            menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});

            $(document).on('click', function() {
                menu.remove();
            });
        });
        loadRecords()
        function loadRecords() {
            var filterdate = $('#filterdate').val();
            $.ajax({
                url: 'loadnoterecords.php',
                method: 'GET',
                data: {filterdate: filterdate},
                success: function(data) {
                    $('#notetable').html(data);
                }
            });
        }


        $('#selectalldate').on('click', function() {
                if ($(this).is(':checked')) {
                    $('#filterdate').prop('disabled', true);
                $.ajax({
                    url: 'loadnoterecords.php',
                    method: 'GET',
                    success: function(data) {
                        $('#notetable').html(data);
                    }
                })
                } else {
                    $('#filterdate').prop('disabled', false);
                    loadRecords();
                    $('#filterdate').on('change', function() {
                var filterdate = $(this).val();
                $.ajax({
                    url: 'loadnoterecords.php',
                    method: 'GET',
                    data: {filterdate: filterdate},
                    success: function(data) {
                        $('#notetable').html(data);
                    }
                });
            });
        };
    });
});

</script>
</body>
</html>
