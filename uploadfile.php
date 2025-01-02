<?php
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $uploadDir = 'ledger/';
    $uploadFile = $uploadDir . basename($file['name']);
    
    // Open the file in binary read mode
    $tempFile = fopen($file['tmp_name'], 'rb');
    $contents = fread($tempFile, filesize($file['tmp_name']));
    fclose($tempFile);

    // Write the contents to the new file
    $destinationFile = fopen($uploadFile, 'wb');
    if (fwrite($destinationFile, $contents)) {
        echo json_encode(['status' => 'success', 'filePath' => $uploadFile]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to write file.']);
    }
    fclose($destinationFile);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
}
?>

