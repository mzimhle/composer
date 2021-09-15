<?php
// Include the autoloader.
include_once __DIR__ . '/../vendor/autoload.php';
// Include database abstraction driver
use \Doctrine\DBAL\DriverManager;
// Connection string to my local database.
$connectionParams = array(
    'dbname' => 'test',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
// Check if all is well.
try {
    $conn = DriverManager::getConnection($connectionParams);
} catch (\Exception $e) {
    print_r($e);
    exit;
}
// Connection string
$sql = "SELECT * FROM user";
// Create the statement and fetch data.
$stmt = $conn->query($sql);
// Return data.
while (($row = $stmt->fetchAssociative()) !== false) {
    echo $row['name'].'<br />';
}