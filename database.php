<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "farmstats_db";
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->database}", 
                $this->username, 
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch(PDOException $e) {
            $this->createDatabaseAndTables();
        }
    }

    private function createDatabaseAndTables() {
        try {
            // Connect without database first
            $temp_conn = new PDO("mysql:host={$this->host}", $this->username, $this->password);
            $temp_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $temp_conn->exec("CREATE DATABASE IF NOT EXISTS {$this->database}");
            $temp_conn->exec("USE {$this->database}");
            
            // Create tables
            $this->createTables($temp_conn);
            
            $this->conn = $temp_conn;
            
        } catch(PDOException $e) {
            die(json_encode([
                'success' => false, 
                'error' => 'Database connection failed: ' . $e->getMessage()
            ]));
        }
    }

    private function createTables($conn) {
        $sql = [
            "CREATE TABLE IF NOT EXISTS farmers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                years_experience INT DEFAULT 0,
                farm_location VARCHAR(255),
                farm_size DECIMAL(10,2),
                farming_method VARCHAR(100),
                land_ownership VARCHAR(50),
                varieties TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS campaigns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                campaign_type VARCHAR(100),
                funding_goal DECIMAL(15,2),
                amount_raised DECIMAL(15,2) DEFAULT 0,
                deadline DATE,
                farmer_id INT,
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (farmer_id) REFERENCES farmers(id)
            )",
            
            "CREATE TABLE IF NOT EXISTS activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                farmer_id INT,
                activity_type VARCHAR(100),
                progress_percent INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (farmer_id) REFERENCES farmers(id)
            )"
        ];

        foreach ($sql as $query) {
            $conn->exec($query);
        }

        // Insert sample data if tables are empty
        $checkFarmers = $conn->query("SELECT COUNT(*) as count FROM farmers")->fetch();
        if ($checkFarmers['count'] == 0) {
            $sampleData = [
                "INSERT INTO farmers (full_name, years_experience, farm_location, farm_size, farming_method, land_ownership, varieties) VALUES
                ('Juan Dela Cruz', 15, 'Nueva Ecija', 5.5, 'Organic', 'Owned', 'IR64, Jasmine'),
                ('Maria Santos', 8, 'Isabela', 3.2, 'Traditional', 'Leased', 'Sinandomeng'),
                ('Pedro Reyes', 20, 'Tarlac', 8.0, 'Modern', 'Owned', 'Hybrid')",
                
                "INSERT INTO campaigns (title, description, campaign_type, funding_goal, amount_raised, deadline, farmer_id) VALUES
                ('Rice Planting Season Support', 'Help fund seeds and fertilizers for the upcoming planting season', 'Equipment', 50000, 25000, '2025-12-31', 1),
                ('Irrigation System Upgrade', 'Modernize farm irrigation for better water management', 'Infrastructure', 75000, 15000, '2025-11-30', 2)",
                
                "INSERT INTO activities (farmer_id, activity_type, progress_percent) VALUES
                (1, 'Land Preparation', 75),
                (2, 'Seed Planting', 50),
                (3, 'Harvesting', 90)"
            ];

            foreach ($sampleData as $query) {
                $conn->exec($query);
            }
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Test database connection
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Test query
    $stmt = $conn->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database test failed: ' . $e->getMessage()]);
    exit;
}
?>