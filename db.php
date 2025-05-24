<?php
$host = 'localhost';
$db   = 'scoring_app';
$user = 'root';
$pass = 'Strong12'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (\PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
?>
