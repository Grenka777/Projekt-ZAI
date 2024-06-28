<?php
$logfile = 'logs/user_logs.txt';
if (file_exists($logfile)) {
    $logs = file($logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($logs as $log) {
        echo "<p>$log</p>";
    }
} else {
    echo "<p>No logs found.</p>";
}

?>