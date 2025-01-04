<?php
session_start();
$branch_id = $_SESSION['branch_id'];
$filename = 'logs/log.txt';
$lines = file($filename);
$branch = substr($lines[count($lines)-1], 22);
if ($branch == $branch_id) {
  echo "true";
} else {
  echo "false";
}


