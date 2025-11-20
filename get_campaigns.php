<?php
require_once 'database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->query("
        SELECT c.*, f.full_name as farmer_name 
        FROM campaigns c 
        LEFT JOIN farmers f ON c.farmer_id = f.id 
        ORDER BY c.created_at DESC
    ");
    $campaigns = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'campaigns' => $campaigns]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>