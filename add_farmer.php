<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Log the request for debugging
file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Add farmer request received\n", FILE_APPEND);

try {
    // Get the raw POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Log the received data
    file_put_contents('debug.log', "Received data: " . print_r($input, true) . "\n", FILE_APPEND);
    
    if (!$input) {
        throw new Exception('No data received or invalid JSON');
    }
    
    // Validate required fields
    $required = ['full_name', 'farm_location', 'farm_size'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Include database connection
    require_once 'database.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    
    // Prepare SQL statement
    $sql = "INSERT INTO farmers (full_name, years_experience, farm_location, farm_size, farming_method, land_ownership, varieties) 
            VALUES (:full_name, :years_experience, :farm_location, :farm_size, :farming_method, :land_ownership, :varieties)";
    
    $stmt = $conn->prepare($sql);
    
    // Process varieties array
    $varieties = '';
    if (isset($input['varieties']) && is_array($input['varieties'])) {
        $varieties = implode(', ', $input['varieties']);
    }
    
    // Execute the statement
    $result = $stmt->execute([
        ':full_name' => trim($input['full_name']),
        ':years_experience' => intval($input['years_experience'] ?? 0),
        ':farm_location' => trim($input['farm_location']),
        ':farm_size' => floatval($input['farm_size']),
        ':farming_method' => $input['farming_method'] ?? '',
        ':land_ownership' => $input['land_ownership'] ?? '',
        ':varieties' => $varieties
    ]);
    
    if ($result) {
        $response = [
            'success' => true, 
            'message' => 'Farmer added successfully',
            'farmer_id' => $conn->lastInsertId()
        ];
        file_put_contents('debug.log', "Farmer added successfully: " . $input['full_name'] . "\n", FILE_APPEND);
    } else {
        throw new Exception('Failed to execute database query');
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(400);
    $error_response = [
        'success' => false, 
        'error' => $e->getMessage(),
        'debug' => [
            'received_data' => $input ?? 'No data',
            'php_version' => PHP_VERSION
        ]
    ];
    file_put_contents('debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode($error_response);
}
?>