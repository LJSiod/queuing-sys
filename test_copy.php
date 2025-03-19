<?php
session_start();
include 'config/db.php';
include 'includes/header.php';
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
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link href="assets/css/styles.css" rel="stylesheet">
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
    </style>

</head>

<body>
    <div class="container-fluid mt-3">
        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <form action="" class="dropzone" method="POST" enctype="multipart/form-data">
                    <input type="file" name="file" id="file">
                </form>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        var myDropzone = new Dropzone("form.dropzone", {
            url: "/upload", // specify the URL for file upload
            method: "post",
            maxFiles: 4,
            maxFilesize: 5, // MB
            acceptedFiles: ".pdf, .jpg, .jpeg, .png, .gif",
            uploadMultiple: false,
            autoProcessQueue: false,
            parallelUploads: 1,
            addRemoveLinks: true,
            dictDefaultMessage: "Drop files here or click to upload",
        });
    </script>
</body>

</html>