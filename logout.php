<?php
session_start();
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1) {
    file_put_contents('status1.txt', '0');
}


if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 3) {
    file_put_contents('status2.txt', '0');
}

session_destroy();
header("Location: login.php");

exit();
?>
