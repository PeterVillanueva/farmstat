<?php
require_once 'database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->query("SELECT * FROM farmers ORDER BY created_at DESC");
    $farmers = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'farmers' => $farmers]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>