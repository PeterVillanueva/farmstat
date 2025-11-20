<?php
require_once 'database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get total farmers
    $stmt = $conn->query("SELECT COUNT(*) as total_farmers FROM farmers");
    $totalFarmers = $stmt->fetch()['total_farmers'];
    
    // Get active crops (estimated)
    $activeCrops = $totalFarmers * 0.65; // 65% of farmers have active crops
    
    // Get total funding
    $stmt = $conn->query("SELECT COALESCE(SUM(amount_raised), 0) as total_funding FROM campaigns");
    $totalFunding = $stmt->fetch()['total_funding'];
    
    // Get recent activities
    $stmt = $conn->query("
        SELECT a.*, f.full_name 
        FROM activities a 
        LEFT JOIN farmers f ON a.farmer_id = f.id 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $activities = $stmt->fetchAll();
    
    // Calculate yield increase (sample data)
    $yieldIncrease = '45%';
    
    $data = [
        'success' => true,
        'stats' => [
            'total_farmers' => $totalFarmers,
            'active_crops' => round($activeCrops),
            'total_funding' => floatval($totalFunding),
            'yield_increase' => $yieldIncrease
        ],
        'activities' => $activities
    ];
    
    echo json_encode($data);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>