<?php
require_once 'database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['title']) || !isset($input['description']) || !isset($input['campaign_type']) || !isset($input['funding_goal'])) {
        throw new Exception('Required fields missing');
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    // Assign to a random farmer for demo purposes
    $stmt = $conn->query("SELECT id FROM farmers ORDER BY RAND() LIMIT 1");
    $farmer = $stmt->fetch();
    $farmerId = $farmer ? $farmer['id'] : 1;
    
    $sql = "INSERT INTO campaigns (title, description, campaign_type, funding_goal, deadline, farmer_id) 
            VALUES (:title, :description, :campaign_type, :funding_goal, :deadline, :farmer_id)";
    
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([
        ':title' => $input['title'],
        ':description' => $input['description'],
        ':campaign_type' => $input['campaign_type'],
        ':funding_goal' => $input['funding_goal'],
        ':deadline' => $input['deadline'] ?? null,
        ':farmer_id' => $farmerId
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Campaign created successfully']);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>