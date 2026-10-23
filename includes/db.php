
<?php
$host = getenv('MYSQLHOST') ?: 'localhost';
$port = (int) (getenv('MYSQLPORT') ?: 3306);
$user = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$database = getenv('MYSQLDATABASE') ?: 'mini_world_decor';

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database,
    $port
);

if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database connection failed. Please try again later.');
}

$conn->set_charset('utf8mb4');
?>
