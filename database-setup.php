<?php
/**
 * Interactive Database Configuration Tool
 * Run this script to view and update database credentials
 */

// Load current configuration
require_once 'config.php';

echo "🔧 Database Configuration Tool\n";
echo "================================\n\n";

// Display current credentials
echo "📊 Current Database Credentials:\n";
echo "Host: " . $db_config['host'] . "\n";
echo "Database: " . $db_config['dbname'] . "\n";
echo "Username: " . $db_config['username'] . "\n";
echo "Password: " . str_repeat('*', strlen($db_config['password'])) . "\n";
echo "Port: " . $db_config['port'] . "\n\n";

// Test current connection
echo "🔍 Testing current connection...\n";
try {
    $pdo = getDatabaseConnection();
    echo "✅ Current connection: SUCCESS\n\n";
} catch (Exception $e) {
    echo "❌ Current connection: FAILED - " . $e->getMessage() . "\n\n";
}

// Ask if user wants to update credentials
echo "Do you want to update database credentials? (y/n): ";
$handle = fopen("php://stdin", "r");
$update = trim(fgets($handle));

if (strtolower($update) === 'y' || strtolower($update) === 'yes') {
    echo "\n🔧 Enter new database credentials:\n";
    
    // Get new credentials
    echo "Host [current: {$db_config['host']}]: ";
    $new_host = trim(fgets($handle));
    if (empty($new_host)) $new_host = $db_config['host'];
    
    echo "Database name [current: {$db_config['dbname']}]: ";
    $new_dbname = trim(fgets($handle));
    if (empty($new_dbname)) $new_dbname = $db_config['dbname'];
    
    echo "Username [current: {$db_config['username']}]: ";
    $new_username = trim(fgets($handle));
    if (empty($new_username)) $new_username = $db_config['username'];
    
    echo "Password [current: " . str_repeat('*', strlen($db_config['password'])) . "]: ";
    $new_password = trim(fgets($handle));
    if (empty($new_password)) $new_password = $db_config['password'];
    
    echo "Port [current: {$db_config['port']}]: ";
    $new_port = trim(fgets($handle));
    if (empty($new_port)) $new_port = $db_config['port'];
    
    // Test new connection before saving
    echo "\n🔍 Testing new connection...\n";
    try {
        $test_dsn = "pgsql:host={$new_host};port={$new_port};dbname={$new_dbname}";
        $test_pdo = new PDO($test_dsn, $new_username, $new_password);
        $test_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ New connection: SUCCESS\n\n";
        
        // Save new configuration
        $new_config = "<?php
// Database Configuration
// Change these values according to your PostgreSQL setup
\$db_config = [
    'host' => '{$new_host}',
    'dbname' => '{$new_dbname}',
    'username' => '{$new_username}',
    'password' => '{$new_password}',
    'port' => '{$new_port}'
];

// Function to get database connection
function getDatabaseConnection() {
    global \$db_config;
    
    try {
        \$dsn = \"pgsql:host={\$db_config['host']};port={\$db_config['port']};dbname={\$db_config['dbname']}\";
        \$pdo = new PDO(\$dsn, \$db_config['username'], \$db_config['password']);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    } catch (PDOException \$e) {
        throw new Exception(\"Database connection failed: \" . \$e->getMessage());
    }
}
?>";
        
        // Backup old config
        if (file_exists('config.php')) {
            copy('config.php', 'config.php.backup.' . date('Y-m-d-H-i-s'));
            echo "📁 Backup created: config.php.backup." . date('Y-m-d-H-i-s') . "\n";
        }
        
        // Write new config
        file_put_contents('config.php', $new_config);
        echo "💾 Configuration saved to config.php\n";
        echo "✅ Database credentials updated successfully!\n\n";
        
        // Show final summary
        echo "📊 New Database Configuration:\n";
        echo "Host: {$new_host}\n";
        echo "Database: {$new_dbname}\n";
        echo "Username: {$new_username}\n";
        echo "Password: " . str_repeat('*', strlen($new_password)) . "\n";
        echo "Port: {$new_port}\n\n";
        
    } catch (Exception $e) {
        echo "❌ New connection: FAILED - " . $e->getMessage() . "\n";
        echo "❌ Configuration NOT saved. Please check your credentials.\n\n";
    }
    
} else {
    echo "\n✅ Configuration unchanged.\n\n";
}

fclose($handle);

echo "🚀 You can now run your application:\n";
echo "   php -S localhost:8000\n";
echo "   Then open: http://localhost:8000/project-with-database.html\n\n";
echo "🔧 To run this tool again: php database-setup.php\n";
?>