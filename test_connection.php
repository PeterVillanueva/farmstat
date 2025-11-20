<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Simple test response
echo json_encode([
    'success' => true, 
    'message' => 'PHP connection is working!',
    'server_time' => date('Y-m-d H:i:s')
]);
?>