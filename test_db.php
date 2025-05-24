<?php
echo "PHP Version: " . phpinfo(INFO_GENERAL) . "<br>";
echo "Available drivers: " . implode(', ', PDO::getAvailableDrivers()) . "<br>";

try {
    $host = 'localhost';
    $db = 'scoring_app';
    $user = 'root';
    $pass = 'Strong12';
    $charset = 'utf8mb4';
    
    echo "Trying to connect to database...<br>";
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful!<br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM judges");
    $result = $stmt->fetch();
    echo "Judges in database: " . $result['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "<br>";
    echo "Error details: " . $e->getTraceAsString();
}
?>
