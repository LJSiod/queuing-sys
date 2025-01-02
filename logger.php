<?php

class Logger {
    private $logFile;

    public function __construct($logFile) {
        $this->logFile = $logFile;
    }

    public function log($message) {
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}

$logger = new Logger('logs/log.txt');

function logAction($action) {
    global $logger;
    $logger->log($action);
}

