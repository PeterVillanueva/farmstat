<?php
session_start();
include 'database.php';

$response = [
    'authenticated' => false,
    'user_id' => null,
    'user_name' => '',
    'user_email' => '',
    'user_role' => ''
];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT id, name, email, role FROM users WHERE id = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $response = [
            'authenticated' => true,
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role']
        ];
    } else {
        // User not found or inactive, destroy session
        session_destroy();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>