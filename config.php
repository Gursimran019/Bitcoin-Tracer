<?php
// Database Configuration
// Change these values according to your PostgreSQL setup
$db_config = [
    'host' => 'localhost',
    'dbname' => 'bitcoin',
    'username' => 'postgres',
    'password' => 'postgres',
    'port' => '5432'
];

// Function to get database connection
function getDatabaseConnection() {
    global $db_config;
    
    try {
        $dsn = "pgsql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']}";
        $pdo = new PDO($dsn, $db_config['username'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}
?>