<?php
session_start();
include 'database.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($role) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        
        // If user is farmer, also add to farmers table
        if ($role == 'farmer') {
            $farmer_sql = "INSERT INTO farmers (user_id, full_name) VALUES (?, ?)";
            $farmer_stmt = $conn->prepare($farmer_sql);
            $farmer_stmt->bind_param("is", $user_id, $name);
            $farmer_stmt->execute();
        }
        
        // Log activity
        $activity_sql = "INSERT INTO activities (title, type) VALUES (?, 'user')";
        $activity_stmt = $conn->prepare($activity_sql);
        $activity_title = "New user registered: $name ($role)";
        $activity_stmt->bind_param("s", $activity_title);
        $activity_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>