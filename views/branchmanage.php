<?php 
session_start();
include '../config/db.php';
include '../includes/header.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT b.id, b.branchname, b.userid, u.fullname FROM branch b LEFT JOIN users u ON b.userid = u.id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = array(
            'id' => $row['id'],
            'branchname' => $row['branchname'],
            'userid' => $row['userid'],
            'fullname' => $row['fullname']
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <style>
        .br-pagebody {
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            max-width: 1000px;
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
    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <h5>Branch Management</h5>    
                    <table class="small table table-sm table-hover" id="manage">
                        <thead>
                            <tr>
                                <th>Branch ID</th>
                                <th>Branch Name</th>
                                <th>Full Name</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dropdownModal" tabindex="-1" role="dialog" aria-labelledby="dropdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="dropdownModalLabel">Assign Branch Handler</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <select class="form-control form-control-sm" name="counters" id="counters">
                            <option>Kim Sabalo</option>
                            <option>Mae Demetais</option>
                            <option>Belle Delos Santos</option>
                        </select>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/sc-2.4.3/datatables.min.js" integrity="sha384-1zOgQnerHMsipDKtinJHWvxGKD9pY4KrEMQ4zNgZ946DseuYh0asCewEBafsiuEt" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            var table = $('#manage').DataTable({
                layout: {
                    topStart: false,
                    bottomEnd: false,
                },
                data: <?php echo json_encode($data); ?>,
                columns: [
                    { data: 'id' },
                    { data: 'branchname' },
                    { data: 'fullname' },
                ],
                deferRender: true,
                scroller: true,
                scrollY: '60vh',
            });

            $(document).on('click', function() {
                $('#actiondropdown').remove();
            });

            $(document).on('contextmenu', '#manage tbody tr', function(e) {
                e.preventDefault();
                $('#actiondropdown').remove();

                var rowData = table.row($(this)).data();
                var id = rowData.id; 
                var menu = $('<div class="dropdown-menu" id="actiondropdown" style="display:block; position:absolute; z-index:1000;">'
                            + '<a class="dropdown-item small" id="assign" href="#"><i class="fa fa-user text-info" aria-hidden="true"></i> Assign Branch Handler</a>'
                            + '</div>').appendTo('body');
                menu.css({top: e.pageY + 'px', left: e.pageX + 'px'});
                
                $('#assign').on('click', function() {
                    $('#dropdownModal').modal('show');
                });
            });
        });
    </script>
</body>
</html>
