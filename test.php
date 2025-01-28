<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="assets/css/styles.css" rel="stylesheet">
    <title>Queueing System</title>
</head>
<body>
    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <input type="text" class="form-control form-control-sm" id="test" name="test">
            <h1 id="result"></h1>
        </div>
    </div>
<script>
    document.getElementById('test').addEventListener('input', function() {
        document.getElementById('result').textContent = this.value;
    });
</script>
</body>
</html>