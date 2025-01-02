<?php 
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Drag and Drop File Upload Test</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Chivo+Mono|Nunito+Sans">
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    
    .drop-area {
      border: 2px dashed #ccc;
      background-color: #fafafa;
      padding: 30px;
      margin-bottom: 20px;
      text-align: center;
    }

    .dragover {
      border-color: #ccc;
      background-color: #e6e6e6;
    }

    .thumbnail {
      max-width: 100px;
      max-height: 100px;
      margin-top: 10px;
    }

  </style>
</head>
<body>
  <div class="container">
    <h1>Drag and Drop File Upload Test</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
      <div class="drop-area" id="drop-area">
        <p>Drag and drop files here or <a href="#" id="click-to-choose">click to choose files</a></p>
        <input type="file" id="file" name="file" style="display: none;">
        <span id="file-name" name="filename" style="display: inline-block; width: 100%;"></span>
        <img id="thumbnail" class="thumbnail" src="#" alt="File Thumbnail" style="display:none;">
      </div>
      <button type="submit" class="btn btn-primary">Upload Files</button>
    </form>
    <ul id="file-list"></ul>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script>
    $(document).ready(function() {
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

      $('#drop-area').on('dragover', function(e) {
        $(this).addClass('dragover');
        e.preventDefault();
      });

      $('#drop-area').on('dragleave', function(e) {
        $(this).removeClass('dragover');
        e.preventDefault();
      });

      $('#drop-area').on('drop', function(e) {
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
    });
  </script>
</body>
</html>

