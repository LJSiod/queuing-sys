<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';
include 'header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: login.php");
    exit();
}

$branch_id = $_SESSION['branch_id'];
$previewid = $_GET['id'];
$query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.cashonhand, qi.cashonhandstatus, qi.activenumber, qi.status, qi.date, qi.datereleased, qi.maturitydate, qi.accinterest, qi.remainingbalance, qi.remarks, qi.note, qi.attachname, qi.servedby, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.id = $previewid";
// $query = "SELECT * FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE ql = $previewid";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (mysqli_num_rows($result) > 0) {
    $id = $row['id'];
    $queueno = str_pad($row['queueno'], 3, '0', STR_PAD_LEFT);
    $branchname = $row['branchname'];
    $branchid = $row['branchid'];
    $clientname = $row['clientname'];
    $datereleased = date('F j, Y', strtotime($row['datereleased']));
    $maturitydate = date('F j, Y', strtotime($row['maturitydate']));
    $loanamount = number_format($row['loanamount'], 2, '.', ',');
    $totalbalance = number_format($row['totalbalance'], 2, '.', ',');
    $onhand = number_format($row['cashonhand'], 2, '.', ',');
    $activenumber = $row['activenumber'];
    $remarks = $row['remarks'];
    $note = $row['note'];
    $ledger = $row['attachname'];
    $type = $row['type'];
    $date = date('F j, Y', strtotime($row['date']));
    $time = date('h:i A', strtotime($row['date']));
    $status = $row['status'];
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

        .br-pagebody {
            margin-top: 10px;
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
          max-width: 70%;
          max-height: 350px;
          margin-bottom: 10px;
          border: 1px solid #e5e5e5;
        }
        
        .dragover {
            background-color: #ccc;
            cursor: move;
        }

    </style>
</head>
<body>
    <div class="mt1 d-print-none"></div>
    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <div class="d-flex justify-content-between">
                <h4>Queue No: <?php echo $queueno; ?></h4>
                <h4><?php echo $branchname; ?> BRANCH</h4>
            </div>
        <form id="noteform" action="" method="post">
            <input type="hidden" name="id" value="<?php echo $ql; ?>">
            <div class="row">
            <div class="col-md-3">
                <label class="small" for="type">Type</label>
                <input type="text" class="form-control form-control-sm" id="type" name="type" value="<?php echo $type; ?>" rows="1" readonly></input>
            </div>
            <div class="col-md-6">
                <label class="small" for="clientname">Client Name</label>
                <input type="text" class="form-control form-control-sm" id="clientname" value="<?php echo $clientname; ?>" name="clientname" rows="1" readonly></input>
            </div>
            <div class="col-md-3">
                <label class="small" for="loanamount">Loan Amount</label>
                <input type="text" class="form-control form-control-sm" id="loanamount" value="<?php echo $loanamount; ?>" name="loanamount" rows="1" readonly></input>
            </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <label class="small" for="datereleased">Date Released</label>
                    <input type="text" class="form-control form-control-sm" id="datereleased" value="<?php echo $datereleased; ?>" name="datereleased" readonly>
                </div>
                <div class="col-md">
                    <label class="small" for="maturitydate">Maturity Date</label>
                    <input type="text" class="form-control form-control-sm" id="maturitydate" value="<?php echo $maturitydate; ?>" name="maturitydate" readonly>
                </div>
                <div class="col-md">
                    <label class="small text-primary font-weight-bold" for="remainingbalance">Overall Remaining Balance</label>
                    <input type="text" class="form-control form-control-sm font-weight-bold" id="remainingbalance" value="<?php echo $totalbalance; ?>" name="remainingbalance" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <label class="small" for="onhand">Client's On-Hand Cash</label>
                    <?php if ($branch_id != 8) { ?>
                        <?php if ($status == "IN QUEUE") { ?>
                        <textarea class="form-control form-control-sm" id="onhand" name="onhand" rows="1"><?php echo $onhand; ?></textarea>
                        <?php } else { ?>
                        <input type="text" class="form-control form-control-sm" id="onhand" value="<?php echo $onhand; ?>" name="onhand" rows="1" readonly></input>
                        <?php } ?>
                    <?php } else { ?>
                        <input type="text" class="form-control form-control-sm" id="onhand" value="<?php echo $onhand; ?>" name="onhand" rows="1"></input>
                    <?php } ?>
                </div>
                <div class="col-md">
                    <label class="small" for="contactno">Branch Active Contact No.</label>
                    <input type="text" class="form-control form-control-sm" id="contactno" value="<?php echo $activenumber; ?>" name="contactno" rows="1" readonly></input>
                </div>
                <div class="col-md">
                    <label class="small font-weight-bold text-danger" for="accinterest">Accrued Interest</label>
                    <input type="text" class="form-control form-control-sm font-weight-bold text-danger" id="accinterest" name="accinterest" rows="1" readonly></input>
                </div>
                <div class="col-md">
                    <label class="small font-weight-bold text-danger" for="totalbalance">Total Balance</label>
                    <input type="text" class="form-control form-control-sm font-weight-bold text-danger" id="totalbalance" name="totalbalance" rows="1" readonly></input>
                </div>            
            </div>
            <div>
            <div class="row">
                <div class="col-md">
                    <label class="small" for="remarks">Remarks</label>
                    <textarea class="form-control form-control-sm overflow-auto" id="remarks" name="remarks" rows="3" readonly><?php echo $remarks; ?></textarea>
                </div>
                <div class="col-md">
                    <?php if ($branch_id != 8) { ?>
                    <label class="small" for="note">Note</label>
                    <textarea class="form-control form-control-sm" id="note" name="note" rows="3" readonly><?php echo $note; ?></textarea>
                    <?php } else { ?>
                    <label class="small" for="note">Note<span class="text-danger small">  *Change value to update</span></label>
                    <textarea class="form-control form-control-sm" id="note" name="note" rows="3"><?php echo $note; ?></textarea>
                    <?php } ?>
                </div>
            </div>
                <div class="col-md">
                    <div class="form-group">
                    <label class="small form-control-label" for="ledger">Ledger Card <span class="text-danger small">  *Click image to preview</span></label>
                        <img class="form-control form-control-sm fileThumbnail mx-auto d-block" id="fileThumbnail" src="<?php echo $ledger; ?>" alt="File Thumbnail" onclick="modalLedgerImage('fileThumbnail')">
                    </div>
                </div>
            </div>
            <?php if ($branch_id == 8) { ?>
            <div class="text-right">
                <a href="print.php?id=<?php echo $id; ?>" class="btn btn-sm btn-warning mt-1 hide">Print Preview</a>
                <?php if ($row['servedby'] == NULL) { ?> 
                <button class="btn btn-sm btn-success mt-1" id="serve">Serve</button>
                <?php } else { ?>
                <button class="btn btn-sm btn-danger mt-1 d-none" id="serve">Serve</button>
                <?php } ?>
                <button class="btn btn-sm btn-primary mt-1" id="notesubmit">Save Changes</button>
                <button type="button" class="btn btn-sm btn-danger mt-1" onclick="window.history.back();">Close</button>
            </div>
            <?php } else { ?>
            <div class="text-right">
                <a href="print.php?id=<?php echo $id; ?>" class="btn btn-sm btn-warning mt-1 hide">Print Preview</a>
                <button type="button" class="btn btn-sm btn-danger mt-1" onclick="window.history.back();">Close</button>
            </div>
            <?php } ?>

        </form>
        </div>
    </div>

       <!-- Modal for expanding the image -->
        <div id="modalImage" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalImageLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 90%;">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="btn btn-sm btn-secondary mr-3" onclick="rotateImage(-90)">
                  <i class="fas fa-undo-alt"></i> Rotate Left
                </button>
                <a id="downloadImageLink" href="" download>
                  <button type="button" class="btn btn-sm btn-primary mr-3">Download</button>
                </a>                   
                <button type="button" class="btn btn-sm btn-secondary" onclick="rotateImage(90)">
                 Rotate Right <i class="fas fa-redo-alt"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <span class="text-muted text-center ml-3 font-italic font-weight-bold">**Scroll to Zoom In/Out**</span>
              <div class="modal-body d-flex justify-content-center align-items-center" id="imageModal">
                <img src="" id="modalImageSrc" title="Scroll to zoom" class="img-fluid" width="100%" style="object-fit: cover;" />
              </div>
            </div>
          </div>
        </div>
        <!-- end modal -->


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    
    
    <script>
            var imageModal = document.getElementById('modalImage');
            imageModal.addEventListener("wheel", function(e) {
                if (e.target.id === 'modalImageSrc') {
                    var zoomLevel = e.wheelDeltaY > 0 ? 1.1 : 1/1.1;
                    var image = document.getElementById('modalImageSrc');
                    var currentWidth = image.width;
                    image.width = currentWidth * zoomLevel;
                    e.preventDefault();
                }
            }, false);
        
            
            var dragging = false;
            var startX = 0;
            var startY = 0;
            var x = 0;
            var y = 0;
            
            const modalImageSrc = document.getElementById('modalImageSrc');
 
            modalImageSrc.addEventListener("mousedown", function(e) {
                e.preventDefault();
                if (e.target === this) {
                    dragging = true;
                    startX = e.clientX;
                    startY = e.clientY;
                    x = imageModal.scrollLeft;
                    y = imageModal.scrollTop;
                    imageModal.style.cursor = "grabbing";
                }
            });
            
            
            modalImageSrc.addEventListener("mouseover", function(e) {
                imageModal.style.cursor = "grab";
            });

            modalImageSrc.addEventListener("mousemove", function(e) {
                if (dragging) {
                    e.preventDefault();
                    var dx = e.clientX - startX;
                    var dy = e.clientY - startY;
                    imageModal.scrollLeft = x - dx;
                    imageModal.scrollTop = y - dy;
                }
            });
            
            modalImageSrc.addEventListener("mouseup", function() {
                dragging = false;
                imageModal.style.cursor = "grab";
            });

        function rotateImage(degrees) {
                const img = document.getElementById('modalImageSrc');
                const currentRotation = img.style.transform.replace(/[^\d.]/g, '') || 0;
                const newRotation = (parseFloat(currentRotation) + degrees) % 360;
                img.style.transform = `rotate(${newRotation}deg)`;
            }
        
            $('#modalImage').on('hidden.bs.modal', function () {
                const img = document.getElementById('modalImageSrc');
                img.style.transform = 'rotate(0deg)';
            });

        function modalLedgerImage(imageId) {
            var fileLink = document.getElementById(imageId).src;
            document.getElementById('modalImageSrc').src = fileLink;
            document.getElementById('downloadImageLink').href = fileLink;
            document.getElementById('downloadImageLink').download = fileLink.split('/').pop();
            $('#modalImage').modal('show'); 
            }

    $(document).ready(function(){
            var loanamount = $('#loanamount').val();
            var loanamount = loanamount.replace(/,/g, '');
            var remainingbalance = $('#remainingbalance').val();
            var remainingbalanceformatted = remainingbalance.replace(/,/g, '');
            var maturitydate = $('#maturitydate').val();
            var today = new Date();
            var diffTime = Math.abs(today - new Date(maturitydate));
            var diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30)); 
            var diffMonths = diffMonths - 1;
            var accinterest = (loanamount * 0.06) * diffMonths;
            var accinterestformatted = parseFloat(accinterest).toLocaleString('en-US',{minimumFractionDigits: 2});
            var totalbalance = parseFloat(remainingbalanceformatted) + parseFloat(accinterest);
            var totalbalanceformatted = parseFloat(totalbalance).toLocaleString('en-US',{minimumFractionDigits: 2});
            $('#totalbalance').val(totalbalanceformatted);
            $('#accinterest').val(accinterestformatted);
            if (accinterest == "NaN") {
                $('#accinterest').val("0.00"); 
            }

        $('#notesubmit').on('click', function() {
            event.preventDefault();
            var cashonhand = document.getElementById('onhand').value;
            var fcashonhand = cashonhand.replace(/,/g, '');
            var note = document.getElementById('note').value;
            var id = <?php echo $previewid; ?>;
            $.ajax({
                url: 'updatenote.php',
                type: 'POST',
                data: {note: note, cashonhand: fcashonhand, id: id},
                success: function(response) {
                    swal({
                        title: "Success",
                        text: "Note updated successfully.",
                        icon: "success",
                        buttons: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = 'admin_dashboard.php';
                    });
                }
            });
        });

        $('#serve').on('click', function() {
            event.preventDefault();
            var id = <?php echo $previewid; ?>;
            $.ajax({
                url: 'serve.php',
                type: 'POST',
                data: {id: id},
                success: function(response) {
                    swal({
                        title: "Success",
                        text: "Serving Client!",
                        icon: "success",
                        buttons: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = 'admin_dashboard.php';
                    });
                }
            });
        });



        const fileThumbnail = document.querySelector('#fileThumbnail');
        if (fileThumbnail.src.endsWith('.pdf')) {
            const pdfLink = fileThumbnail.src;
            const loadingTask = pdfjsLib.getDocument(pdfLink);
            loadingTask.promise.then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                    const viewport = page.getViewport({ scale: 1.0 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise.then(function() {
                        fileThumbnail.src = canvas.toDataURL('image/png');
                    });
                });
            });
        }
    
        });
    </script>
    </body>
</html>



