<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit();
}

$branch_id = $_SESSION['branch_id'];
$id = $_SESSION['user_id'];
$id = $_GET['id'];
$query = "SELECT qi.id, qi.queueno, qi.branchid, qi.type, qi.clientname, qi.loanamount, qi.totalbalance, qi.cashonhand, qi.cashonhandstatus, qi.datereceived, qi.status, qi.date, qi.datereleased, qi.maturitydate, qi.remarks, qi.note, qi.attachname, b.branchname FROM queueinfo qi LEFT JOIN branch b ON qi.branchid = b.id WHERE qi.id = $id";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<style>

        .mt1 {
            margin-top: 50px;
        }

        .br-pagebody {
            margin-left: auto;
            margin-right: auto;
            max-width: 1100px;
            font-family: sans-serif;
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
            max-height: 500px;
            overflow: auto;
        }

        .counter {
            max-height: 285px;
            overflow: auto;
        }

        .disabled {
            text-decoration: line-through;     
        }

        p {
            margin: 5px;
            margin-bottom: 0px;
        }

        .fileThumbnail {
          width: 100%;
          height: 100%;
          max-width: 950px;
          max-height: 450px;
          margin-bottom: 10px;
          border: 1px solid #e5e5e5;
        }

        @media print {
            body {
                background-color: white;
            }
        }
    </style>
<body>
    <div class="mt1 d-print-none"></div>
    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <div class="row">
                <div class="col">
                    <span><b>Branch:</b> <?= $row['branchname'] ?></span>
                </div>
                <div class="col">
                    <span><b>Name:</b> <?= $row['clientname'] ?></span>
                </div>
                <div class="col">
                    <span><b>Type:</b> <?= $row['type'] ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <span><b>Loan Amount:</b> <?= number_format($row['loanamount'], 2, '.', ',') ?></span>
                    <input type="hidden" id="loanamount" value="<?= $row['loanamount'] ?>">
                </div>
                <div class="col">
                    <span><b>Date Released:</b> <?= date('F j, Y', strtotime($row['datereleased'])) ?></span>
                </div>
                <div class="col">
                    <span><b>Maturity Date:</b> <?= date('F j, Y', strtotime($row['maturitydate'])) ?></span>
                    <input type="hidden" id="maturitydate" value="<?= $row['maturitydate'] ?>">
                </div>
            </div>

            <div class="row">
                <div class="col text-primary font-weight-bold">
                    <span><b>Overall Balance:</b> </span><span class="text-dark"><?= number_format($row['totalbalance'], 2, '.', ',') ?></span>
                    <input type="hidden" id="remainingbalance" value="<?= $row['totalbalance'] ?>">
                </div>
                <div class="col">
                    <span><b>On-hand Cash:</b> <?= number_format($row['cashonhand'], 2, '.', ',') ?></span>
                </div>
                <div class="col">
                    <span><b>Date Letter Received:</b> <?= date('F j, Y', strtotime($row['datereceived'])) ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col text-danger font-weight-bold">
                    <span><b>Accrued Interest:</b> </span><span class="text-dark" id="accinterest"></span>
                </div>
                <div class="col text-danger font-weight-bold">
                    <span><b>Accrued Penalty:</b> </span><span class="text-dark" id="accpenalty"></span>
                </div>
                <div class="col text-danger font-weight-bold">
                    <span><b>Total Balance:</b> </span><span class="text-dark" id="totalbalance"></span>
                </div>
            </div>

            <span><b>Remarks:</b> <?= $row['remarks'] ?></span>
            <hr>
            <img class="form-control form-control-sm fileThumbnail mx-auto d-block" id="fileThumbnail" src="../<?= $row['attachname'] ?>" alt="File Thumbnail">
            <div class="text-right">
                <button class="btn btn-sm btn-primary d-print-none" onclick="window.print();">Print</button>
                <button type="button" class="btn btn-sm btn-danger d-print-none" onclick="window.history.back();">Close</button>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function(){
        var loanamount = $('#loanamount').val();
        var loanamount = loanamount.replace(/,/g, '');
        var remainingbalance = $('#remainingbalance').val();
        var remainingbalanceformatted = remainingbalance.replace(/,/g, '');
        var maturitydate = $('#maturitydate').val();
        var today = moment();
        var maturitydate = moment(maturitydate);
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
        var accinterestformatted = parseFloat(accinterest).toLocaleString('en-US',{minimumFractionDigits: 2});
        var accpenaltyformatted = parseFloat(accpenalty).toLocaleString('en-US',{minimumFractionDigits: 2});
        var totalbalance = parseFloat(remainingbalanceformatted) + parseFloat(accpenalty) + parseFloat(accinterest);
        var totalbalanceformatted = parseFloat(totalbalance).toLocaleString('en-US',{minimumFractionDigits: 2});
        $('#totalbalance').text(totalbalanceformatted);
        if (totalbalance == "NaN") {
            $('#totalbalance').text("0.00");
        }
        $('#accinterest').text(accinterestformatted);
        if (accinterest == "NaN") {
            $('#accinterest').text("0.00");
        }
        $('#accpenalty').text(accpenaltyformatted);
        if (accpenalty == "NaN") {
            $('#accpenalty').text("0.00"); 
        }

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
