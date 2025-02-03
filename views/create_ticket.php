<?php
session_start();
date_default_timezone_set('Asia/Manila');
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}

$branch_id = $_SESSION['branch_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Chivo+Mono|Nunito+Sans">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <style>

        .mt1 {
            margin-top: 50px;
        }

        .br-pagebody {
            margin-left: auto;
            margin-right: auto;
            max-width: 1100px;
        }

        .br-section-wrapper {
            background-color: #fff;
            padding: 20px;
            margin-left: 0px;
            margin-right: 0px;
            box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.21);
        }

        .fileThumbnail {
            width: 100%;
            height: 100%;
            max-width: 710px;
            max-height: 230px;
            border: 3px solid #e5e5e5;
            border-style: dashed;
        }

        .dragover {
            border-color: rgb(252, 160, 0);
            background-color:rgba(0, 252, 4, 0.09);
        }

    </style>
</head>
<body>
    <!-- <div id="overlay" class="text-center text-light strong" style="top: 0; left: 0; width: 100vw; height: 100vh;">Drop Files Anywhere  <i class="fa fa-download"></i></div> -->
    <div class="mt1 d-print-none"></div>
    <div class="br-pagebody">
        <div class="br-section-wrapper" id="addqueue">
        <div class="d-flex justify-content-between">
          <h4>Add Queue</h4>
          <span class="text-danger small">*<i><u>Double Check entries before Submitting</u></i></span>
        </div>
        <form id="queueform" action="" method="post" enctype="multipart/form-data">
            <div class="row">
            <div class="col">
                <label class="small" for="type">Type</label>
                <select class="form-control form-control-sm" id="type" name="type">
                    <option>DL</option>
                    <option>PN</option>
                    <option>BS</option>
                </select>
            </div>
            <div class="col"> 
                <label class="small" for="daterec">Date Letter Received</label>
                <input type="date" class="form-control form-control-sm" id="daterec" name="daterec" rows="1" required></input>
            </div>
            <div class="col">
                <label class="small" for="clientname">Client Name</label>
                <input type="text" class="form-control form-control-sm" id="clientname" name="clientname" rows="1" required></input>
            </div>
            <div class="col">
                <label class="small" for="loanamount">Loan Amount</label>
                <input type="number" class="form-control form-control-sm" id="loanamount" name="loanamount" rows="1" required></input>
            </div>
            </div>
            <div class="row">
                <div class="col">
                    <label class="small" for="datereleased">Date Released</label>
                    <input type="date" class="form-control form-control-sm" id="datereleased" name="datereleased" required>
                </div>
                <div class="col">
                    <label class="small" for="maturitydate">Maturity Date</label>
                    <input type="date" class="form-control form-control-sm" id="maturitydate" name="maturitydate" required>
                </div>
                <div class="col">
                    <label class="small" for="remainingbalance">Overall Remaining Balance</label>
                    <input type="number" class="form-control form-control-sm" id="remainingbalance" name="remainingbalance" required>
                </div>
                <div class="col">
                    <label class="small" for="onhand">Client's On-Hand Cash</label>
                    <input type="number" class="form-control form-control-sm" id="onhand" name="onhand" rows="1" required></input>
                </div>
            </div>
            <div class="row">
            <div class="col">
                <label class="small" for="accinterest">Accrued Interest</label> 
                <input type="text" class="form-control form-control-sm" id="accinterest" name="accinterest" rows="1" readonly></input>
            </div>
            <div class="col">
                <label class="small" for="accpenalty">Accrued Penalty</label>
                <input type="text" class="form-control form-control-sm" id="accpenalty" name="accpenalty" rows="1" readonly></input>
            </div>
            <div class="col">
                <label class="small" for="totalbalance">Total Balance</label>
                <input type="text" class="form-control form-control-sm" id="totalbalance" name="totalbalance" rows="1" readonly></input>
            </div>            
            </div>
            <div class="row">
            <div class="col">
                <label class="small" for="remarks">Remarks</label>
                <textarea class="form-control form-control-sm" id="remarks" name="remarks" rows="12" required></textarea>
            </div>
            <div class="col">
                <div class="form-group" id="drop-area">
                <label class="small form-control-label" for="ledger">Drag and drop files here<span class="text-danger ml-1" style="font-size: 0.6rem">*PDF/Image files accepted</span></label>
                    <img class="form-control form-control-sm fileThumbnail" id="thumbnail" src="../assets/image/drop.jpg" alt="File Thumbnail" ondrop="">
                    <input type="file" id="file" name="file" style="display: none;">
                    <div class="d-flex">
                        or <span class="btn btn-sm btn-secondary ml-2 mt-2" style="font-size: 0.7rem" id="click-to-choose">Choose files</span>
                        <span class="small mt-2 ml-2" id="file-name" name="filename">No file Chosen</span>
                        </div>
                    </div>
                      <!-- <div class="drop-area" id="drop-area">
                        <p>Drag and drop files here or <a href="#" id="click-to-choose">click to choose files</a></p>
                        <input type="file" id="file" name="file" style="display: none;">
                        <span id="file-name" name="filename" style="display: inline-block; width: 100%;"></span>
                      </div> -->
            </div>
            </div>
            <div class="text-right">
                <button type="submit" id="queuesubmit" class="btn btn-sm btn-primary mt-1">Add Queue</button>
                <button id="closebtn" class="btn btn-sm btn-danger mt-1" onclick="window.location.href = 'dashboard.php';">Close</button>
            </div>
        </form>
        </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
     $(document).ready(function(){

      //START NEW DRAG AND DROP
      $('#click-to-choose').on('click', function(e) {
        e.preventDefault();
        $('#file').click();
      });

        document.getElementById('file').addEventListener('change', function() {
        document.getElementById('file-name').textContent = this.files[0].name;
      });

        document.getElementById('thumbnail').addEventListener('load', function() {
        var file = document.getElementById('file').files[0];
        if (file) {
          document.getElementById('file-name').textContent = file.name;
        }
        });

      $('#file').on('change', function() {
        var file = this.files[0];
        if (file) {
          if (file.type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#thumbnail').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
          } else if (file.type.match('application/pdf')) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var loadingTask = pdfjsLib.getDocument({ data: e.target.result });
              loadingTask.promise.then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                  var scale = 1.0;
                  var viewport = page.getViewport({ scale: scale });
                  var canvas = document.createElement('canvas');
                  var context = canvas.getContext('2d');
                  canvas.height = viewport.height;
                  canvas.width = viewport.width;
                  page.render({
                    canvasContext: context,
                    viewport: viewport
                  }).promise.then(function() {
                    $('#thumbnail').attr('src', canvas.toDataURL('image/png')).show();
                  });
                });
              });
            };
            reader.readAsArrayBuffer(file);
          }
        }
      });

      $('body, #addqueue').on('dragover', function(e) {
        $(this).addClass('dragover');
        e.preventDefault();
      });

      $('body, #addqueue').on('dragleave', function(e) {
        $(this).removeClass('dragover');
        e.preventDefault();
      });

      $('body, #addqueue').on('drop', function(e) {
        $(this).removeClass('dragover');
        e.preventDefault();
        var file = e.originalEvent.dataTransfer.files[0];
        if (file) {
          if (file.type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#thumbnail').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
          } else if (file.type.match('application/pdf')) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var loadingTask = pdfjsLib.getDocument({ data: e.target.result });
              loadingTask.promise.then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                  var scale = 1.0;
                  var viewport = page.getViewport({ scale: scale });
                  var canvas = document.createElement('canvas');
                  var context = canvas.getContext('2d');
                  canvas.height = viewport.height;
                  canvas.width = viewport.width;
                  page.render({
                    canvasContext: context,
                    viewport: viewport
                  }).promise.then(function() {
                    $('#thumbnail').attr('src', canvas.toDataURL('image/png')).show();
                  });
                });
              });
            };
            reader.readAsArrayBuffer(file);
          }
          $('#file').prop('files', e.originalEvent.dataTransfer.files);
        }
      });

      //END NEW DRAG AND DROP

      $('#loanamount, #maturitydate').on('input', function() {
          var loanamount = parseFloat($('#loanamount').val());
          var today = moment();
          var maturitydate = moment($('#maturitydate').val());
          var diffMonths = maturitydate.diff(today, 'months');
          var accinterest = (loanamount * 0.06) * Math.abs(diffMonths);
          var penaltyCount = 0;
          var currentDate = maturitydate.clone().add(1, 'days');

          while (currentDate <= today) {
              if (currentDate.date() === 15 || currentDate.isSame(currentDate.clone().endOf('month'), 'day')) {
                  penaltyCount++;
              }
              currentDate.add(1, 'days');
          }
          console.log(penaltyCount);
          var accpenalty = (loanamount * 0.01) * penaltyCount;
          $('#accinterest').val(accinterest.toLocaleString('en-US', { minimumFractionDigits: 2 }));
          $('#accpenalty').val(accpenalty.toLocaleString('en-US', { minimumFractionDigits: 2 }));
          if (isNaN(accinterest)) {
              $('#accinterest').val("0.00");
          }
          if (isNaN(accpenalty)) {
              $('#accpenalty').val("0.00");
          }
      });

      $('#remainingbalance, #maturitydate').on('input', function(){
          var remainingbalance = parseFloat($('#remainingbalance').val().replace(/,/g, ''));
          var accinterest = parseFloat($('#accinterest').val().replace(/,/g, ''));
          var accpenalty = parseFloat($('#accpenalty').val().replace(/,/g, ''));
          var totalbalance = remainingbalance + accinterest + accpenalty;
          $('#totalbalance').val(totalbalance.toLocaleString('en-US', {minimumFractionDigits: 2}));
          if (isNaN(totalbalance)) {
            $('#totalbalance').val("0.00");
          }
      });

        //         let totalCount = 0;

  //         let currentDate = new Date(maturitydate);
  //         let end = new Date(today);

  //         while (currentDate <= end) {
  //         let day = currentDate.getDate();
  //         let month = currentDate.getMonth() + 1; // Months are 0-based in JS
  //         let year = currentDate.getFullYear();

  //         // Get the last day of the month
  //         let lastDay = new Date(year, month, 0).getDate();

  //         // Count if it's the 15th or the last day of the month
  //         if (day === 15 || day === lastDay) {
  //             totalCount++;
  //         }
          
  //         // Move to the next day
  //         currentDate.setDate(currentDate.getDate() + 1);
  //  }
          // totalCount --;
          // if (totalCount < 0) {
          //     totalCount = 0;
          // } 

        $('#queueform').submit(function(e) {
          if ($('#file').val() == '') {
          swal({
            title: "Error",
            text: "Please select a file to upload",
            icon: "error",
            button: "OK",
          });
          return false;
        }
          e.preventDefault();
          var formData = new FormData(this);
          var branchId = <?= $branch_id; ?>;
          $.ajax({
            type: 'POST',
            url: '../upload.php',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
              swal({
                title: "Uploading...",
                text: "Please wait while the file is being uploaded.",
                icon: "info",
                buttons: false
              });
            },

            success: function(response) {
              swal({
                title: "Success!",
                text: "Queue added successfully!",
                icon: "success",
                buttons: false,
                timer: 1500
              }).then(function() {
                window.location.href = "dashboard.php";
              });
            },
            error: function(xhr, status, error) {
              swal({
                title: "Error!",
                text: "Something went wrong!",
                icon: "error",
                buttons: false,
                timer: 1500
              }).then(function() {
                $('#queueform')[0].reset();
                $('#fileThumbnail').attr('src', 'image\drop.jpg');
                $('#file-name').text('');
              });
            }
          });
        });
});

    </script>
    </body>
</html>

