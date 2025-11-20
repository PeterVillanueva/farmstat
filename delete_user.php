<?php
session_start();
include 'database.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Check if user ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
        exit();
    }
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First, check if the user exists and get their name for logging
        $user_sql = "SELECT name, role FROM users WHERE id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        
        if ($user_result->num_rows === 0) {
            throw new Exception("User not found");
        }
        
        $user_data = $user_result->fetch_assoc();
        $user_name = $user_data['name'];
        $user_role = $user_data['role'];
        
        // If user is a farmer, also delete from farmers table
        if ($user_role === 'farmer') {
            $delete_farmer_sql = "DELETE FROM farmers WHERE user_id = ?";
            $delete_farmer_stmt = $conn->prepare($delete_farmer_sql);
            $delete_farmer_stmt->bind_param("i", $user_id);
            $delete_farmer_stmt->execute();
        }
        
        // Delete the user
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            // Log the activity
            $activity_sql = "INSERT INTO activities (title, type) VALUES (?, 'user')";
            $activity_stmt = $conn->prepare($activity_sql);
            $activity_title = "User deleted: $user_name (ID: $user_id)";
            $activity_stmt->bind_param("s", $activity_title);
            $activity_stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            throw new Exception("Failed to delete user");
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'No user ID provided']);
}

exit();
?>