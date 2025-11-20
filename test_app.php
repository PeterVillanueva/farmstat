<?php
/**
 * Quick Application Test
 * Run this to verify the MVC setup is working
 */

echo "Testing FarmStat MVC Application...\n\n";

// Test 1: Check if config loads
echo "1. Testing configuration...\n";
try {
    require_once __DIR__ . '/app/config/config.php';
    echo "   ✓ Config loaded successfully\n";
    echo "   ✓ BASE_URL: " . BASE_URL . "\n";
    echo "   ✓ APP_URL: " . APP_URL . "\n";
} catch (Exception $e) {
    echo "   ✗ Config failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check database connection
echo "\n2. Testing database connection...\n";
try {
    require_once __DIR__ . '/app/config/database.php';
    $db = Database::getConnection();
    echo "   ✓ Database connection successful\n";
    
    // Test query
    $stmt = $db->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "   ✓ Database query test passed\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "   Note: Make sure MySQL is running and database exists\n";
}

// Test 3: Check autoloader
echo "\n3. Testing autoloader...\n";
try {
    $user = new User();
    echo "   ✓ User model loaded successfully\n";
    
    $controller = new AuthController();
    echo "   ✓ AuthController loaded successfully\n";
} catch (Exception $e) {
    echo "   ✗ Autoloader failed: " . $e->getMessage() . "\n";
}

// Test 4: Check file structure
echo "\n4. Checking file structure...\n";
$requiredDirs = [
    'app/controllers',
    'app/models',
    'app/views',
    'app/config',
    'app/core',
    'assets/css',
    'assets/js'
];

foreach ($requiredDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "   ✓ {$dir}/ exists\n";
    } else {
        echo "   ✗ {$dir}/ missing\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Application test complete!\n";
echo "\nTo access the application:\n";
echo "1. Make sure XAMPP Apache and MySQL are running\n";
echo "2. Open browser and go to: " . APP_URL . "\n";
echo "3. Default login: admin@farmstat.com / password\n";
echo str_repeat("=", 50) . "\n";

