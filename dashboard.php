<?php
// Only start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php';
include 'helpers.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user statistics for admin
if ($_SESSION['user_role'] == 'admin') {
    $stats = [];
    
    // Total users
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = $conn->query($sql);
    $stats['total_users'] = $result->fetch_assoc()['total'];
    
    // Total farmers
    $sql = "SELECT COUNT(*) as total FROM farmers";
    $result = $conn->query($sql);
    $stats['total_farmers'] = $result->fetch_assoc()['total'];
    
    // Total funding
    $sql = "SELECT SUM(current_amount) as total FROM campaigns";
    $result = $conn->query($sql);
    $stats['total_funding'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Active campaigns
    $sql = "SELECT COUNT(*) as total FROM campaigns WHERE status = 'active'";
    $result = $conn->query($sql);
    $stats['active_campaigns'] = $result->fetch_assoc()['total'];
    
    // Recent activities - with error handling
    try {
        $sql = "SELECT ua.*, u.name as user_name 
                FROM user_activities ua 
                JOIN users u ON ua.user_id = u.id 
                ORDER BY ua.created_at DESC 
                LIMIT 10";
        $result = $conn->query($sql);
        $activities = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
        } else {
            $activities = [];
        }
    } catch (Exception $e) {
        $activities = [];
    }
}

// Get recent logins for admin dashboard
if ($_SESSION['user_role'] == 'admin') {
    try {
        $recent_logins_sql = "SELECT u.name, u.email, u.role, u.last_login 
                             FROM users u 
                             WHERE u.last_login IS NOT NULL 
                             ORDER BY u.last_login DESC 
                             LIMIT 10";
        $recent_logins_result = $conn->query($recent_logins_sql);
        $recent_logins = [];
        if ($recent_logins_result) {
            while ($row = $recent_logins_result->fetch_assoc()) {
                $recent_logins[] = $row;
            }
        } else {
            $recent_logins = [];
        }
    } catch (Exception $e) {
        $recent_logins = [];
    }
}

// Get user-specific data for CLIENT users
if ($_SESSION['user_role'] == 'client') {
    $user_id = $_SESSION['user_id'];
    
    // Get user profile data
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    
    // Get user activities
    try {
        $activity_sql = "SELECT * FROM user_activities WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
        $activity_stmt = $conn->prepare($activity_sql);
        $activity_stmt->bind_param("i", $user_id);
        $activity_stmt->execute();
        $activity_result = $activity_stmt->get_result();
        $user_activities = [];
        while ($row = $activity_result->fetch_assoc()) {
            $user_activities[] = $row;
        }
    } catch (Exception $e) {
        $user_activities = [];
    }
    
    // Get client statistics
    $client_stats = [];
    
    // Total campaigns supported
    $campaigns_sql = "SELECT COUNT(DISTINCT campaign_id) as total FROM campaign_supporters WHERE user_id = ?";
    $campaigns_stmt = $conn->prepare($campaigns_sql);
    $campaigns_stmt->bind_param("i", $user_id);
    $campaigns_stmt->execute();
    $campaigns_result = $campaigns_stmt->get_result();
    $client_stats['supported_campaigns'] = $campaigns_result->fetch_assoc()['total'] ?? 0;
    
    // Total farmers supported
    $farmers_sql = "SELECT COUNT(DISTINCT f.id) as total 
                   FROM farmers f 
                   JOIN campaigns c ON f.id = c.farmer_id 
                   JOIN campaign_supporters cs ON c.id = cs.campaign_id 
                   WHERE cs.user_id = ?";
    $farmers_stmt = $conn->prepare($farmers_sql);
    $farmers_stmt->bind_param("i", $user_id);
    $farmers_stmt->execute();
    $farmers_result = $farmers_stmt->get_result();
    $client_stats['supported_farmers'] = $farmers_result->fetch_assoc()['total'] ?? 0;
    
    // Total amount donated
    $donations_sql = "SELECT SUM(amount) as total FROM campaign_supporters WHERE user_id = ?";
    $donations_stmt = $conn->prepare($donations_sql);
    $donations_stmt->bind_param("i", $user_id);
    $donations_stmt->execute();
    $donations_result = $donations_stmt->get_result();
    $client_stats['total_donated'] = $donations_result->fetch_assoc()['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['user_role'] == 'admin' ? 'Admin Dashboard' : 'Client Dashboard'; ?> - FarmStats</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php if ($_SESSION['user_role'] == 'admin'): ?>
        <!-- KEEP EXISTING ADMIN DASHBOARD - DON'T MODIFY -->
        <!-- Your existing admin dashboard HTML goes here -->
        
    <?php else: ?>
        <!-- CLIENT DASHBOARD -->
        <div class="dashboard">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-brand">
                        <i class="fas fa-seedling logo"></i>
                        <span>FarmStats</span>
                    </div>
                </div>
                
                <nav class="sidebar-nav">
                    <ul>
                        <li class="nav-item active">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </li>
                        <li class="nav-item">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>Support Campaigns</span>
                        </li>
                        <li class="nav-item">
                            <i class="fas fa-tractor"></i>
                            <span>Browse Farmers</span>
                        </li>
                        <li class="nav-item">
                            <i class="fas fa-history"></i>
                            <span>My Activities</span>
                        </li>
                        <li class="nav-item">
                            <i class="fas fa-user-cog"></i>
                            <span>Profile Settings</span>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Header -->
                <header class="dashboard-header">
                    <div class="header-content">
                        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h1>
                        <div class="admin-indicator">
                            <span class="admin-badge">
                                <i class="fas fa-user"></i>
                                Supporter Account
                            </span>
                            <span class="last-login">
                                Last login: <?php echo htmlspecialchars($_SESSION['last_login']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="admin-menu">
                        <div class="admin-info">
                            <div class="admin-avatar">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </div>
                        <a href="logout.php" class="btn btn-outline btn-sm">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="page-content">
                    <!-- Client Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $client_stats['supported_campaigns'] ?? 0; ?></div>
                                <div class="stat-label">Campaigns Supported</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    Making a difference
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-tractor"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $client_stats['supported_farmers'] ?? 0; ?></div>
                                <div class="stat-label">Farmers Helped</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    Supporting agriculture
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-donate"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">$<?php echo number_format($client_stats['total_donated'] ?? 0, 2); ?></div>
                                <div class="stat-label">Total Donated</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    Creating impact
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $user_data['login_count'] ?? 0; ?></div>
                                <div class="stat-label">Total Logins</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    Active supporter
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="content-row">
                        <!-- Profile Information -->
                        <div class="content-card">
                            <div class="page-header">
                                <h2>
                                    <i class="fas fa-user-circle"></i>
                                    My Profile
                                </h2>
                                <div class="header-actions">
                                    <a href="update_profile.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                        Edit Profile
                                    </a>
                                </div>
                            </div>
                            
                            <div class="profile-info">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Full Name</label>
                                        <span><?php echo htmlspecialchars($user_data['name'] ?? $_SESSION['user_name']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Email Address</label>
                                        <span><?php echo htmlspecialchars($user_data['email'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Member Since</label>
                                        <span><?php echo htmlspecialchars($user_data['created_at'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label>Account Type</label>
                                        <span class="user-role role-supporter">Supporter</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="content-card">
                            <div class="page-header">
                                <h2>
                                    <i class="fas fa-bolt"></i>
                                    Quick Actions
                                </h2>
                            </div>
                            
                            <div class="quick-actions-grid">
                                <a href="get_campaigns.php" class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Support Campaigns</h4>
                                        <p>Help farmers achieve their goals</p>
                                    </div>
                                </a>
                                
                                <a href="get_farmers.php" class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-tractor"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Browse Farmers</h4>
                                        <p>Discover local farmers</p>
                                    </div>
                                </a>
                                
                                <a href="update_profile.php" class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-user-edit"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>Update Profile</h4>
                                        <p>Manage your account</p>
                                    </div>
                                </a>
                                
                                <a href="#" class="action-card">
                                    <div class="action-icon">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>View History</h4>
                                        <p>See your contributions</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="content-card large">
                        <div class="page-header">
                            <h2>
                                <i class="fas fa-history"></i>
                                Recent Activities
                            </h2>
                        </div>
                        
                        <div class="activity-list">
                            <?php if (!empty($user_activities)): ?>
                                <?php foreach ($user_activities as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-<?php 
                                                switch($activity['activity_type']) {
                                                    case 'login': echo 'sign-in-alt'; break;
                                                    case 'donation': echo 'donate'; break;
                                                    case 'profile_update': echo 'user-edit'; break;
                                                    default: echo 'check-circle';
                                                }
                                            ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title"><?php echo htmlspecialchars($activity['description']); ?></div>
                                            <div class="activity-time"><?php echo $activity['created_at']; ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    <p>No recent activities found</p>
                                    <small>Your activities will appear here</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    <?php endif; ?>

    <script>
        // Simple navigation handling
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>