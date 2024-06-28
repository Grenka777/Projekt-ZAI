<?php
$host = 'db';  // Имя сервиса базы данных из docker-compose.yml
$dbname = 'loginsystem';
$username = 'root';
$password = 'student@12345';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
function log_event($user_id, $action) {
    $log_dir = __DIR__ . '/../logs';
    $logfile = $log_dir . '/user_logs.txt'; 
    
    // Check if the logs directory exists, if not, create it
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $timestamp = date('Y-m-d H:i:s');

    $log_entry = "[$timestamp] UserID: $user_id, Action: $action, IP: $ip_address, UserAgent: $user_agent\n";

    file_put_contents($logfile, $log_entry, FILE_APPEND | LOCK_EX);
}
?>