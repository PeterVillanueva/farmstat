<?php
session_start();
include 'dashboard.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmStat - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-css.css">
</head>
<body>
    <!-- Dashboard -->
    <div id="dashboard" class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <span class="logo">🌾</span>
                    <span>FarmStat Admin</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item active" data-page="overview">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </li>
                    <li class="nav-item" data-page="userManagement">
                        <i class="fas fa-users"></i>
                        <span>User Management</span>
                    </li>
                    <li class="nav-item" data-page="farmers">
                        <i class="fas fa-tractor"></i>
                        <span>Farmers</span>
                    </li>
                    <li class="nav-item" data-page="campaigns">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Campaigns</span>
                    </li>
                    <li class="nav-item" data-page="analytics">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </li>
                    <li class="nav-item" data-page="system">
                        <i class="fas fa-cog"></i>
                        <span>System Settings</span>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <h1 id="pageTitle">Admin Dashboard</h1>
                    <div class="admin-indicator">
                        <span class="admin-badge">Administrator</span>
                        <span class="last-login">
                            Last login: 
                            <?php 
                                if (isset($_SESSION['last_login'])) {
                                    echo date('F j, Y, g:i A', strtotime($_SESSION['last_login']));
                                } else {
                                    echo 'Today, ' . date('g:i A');
                                }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="header-right">
                    <div class="admin-menu">
                        <div class="admin-info">
                            <div class="admin-avatar" id="adminAvatar">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span id="adminWelcome"><?php echo $_SESSION['user_name']; ?></span>
                        </div>
                        <button id="logoutBtn" class="btn btn-outline">Logout</button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                <!-- Overview Page -->
                <div id="overviewPage" class="page active">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number" id="totalUsers"><?php echo $stats['total_users']; ?></span>
                                <span class="stat-label">Total Users</span>
                                <span class="stat-trend positive">+12% this month</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-tractor"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number" id="totalFarmers"><?php echo $stats['total_farmers']; ?></span>
                                <span class="stat-label">Rice Farmers</span>
                                <span class="stat-trend positive">+8% this month</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number" id="totalFunding">₱<?php echo number_format($stats['total_funding'], 2); ?></span>
                                <span class="stat-label">Total Funding</span>
                                <span class="stat-trend positive">+15% this month</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-number" id="yieldIncrease">156%</span>
                                <span class="stat-label">Avg Yield Increase</span>
                                <span class="stat-trend positive">+5% this season</span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-content">
                        <div class="content-row">
                            <div class="content-card large">
                                <h3>Platform Activity</h3>
                                <div class="activity-chart-container">
                                    <canvas id="activityChart"></canvas>
                                </div>
                            </div>
                            
                            <div class="content-card">
                                <h3>Recent Activity</h3>
                                <div class="activity-list" id="activityList">
                                    <?php if (!empty($activities)): ?>
                                        <?php foreach ($activities as $activity): ?>
                                            <div class="activity-item">
                                                <div class="activity-icon">
                                                    <i class="fas fa-<?php echo getActivityIcon($activity['type']); ?>"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="activity-title"><?php echo $activity['title']; ?></div>
                                                    <div class="activity-time"><?php echo time_elapsed_string($activity['created_at']); ?></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="activity-item">
                                            <div class="activity-content">
                                                <div class="activity-title">No recent activities</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Add this to the overview page after the stats grid -->
<div class="content-row">
    <div class="content-card">
        <h3>Recent User Logins</h3>
        <div class="recent-logins">
            <?php if (!empty($recent_logins)): ?>
                <?php foreach ($recent_logins as $login): ?>
                    <div class="login-item">
                        <div class="login-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="login-info">
                            <div class="login-user">
                                <strong><?php echo htmlspecialchars($login['name']); ?></strong>
                                <span class="user-role role-<?php echo $login['role']; ?>"><?php echo ucfirst($login['role']); ?></span>
                            </div>
                            <div class="login-time">
                                <?php echo time_elapsed_string($login['last_login']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">No recent logins</div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="content-card">
        <h3>Login Statistics</h3>
        <div class="login-stats">
            <div class="login-stat">
                <span class="stat-number"><?php echo $stats['total_users']; ?></span>
                <span class="stat-label">Total Users</span>
            </div>
            <div class="login-stat">
                <span class="stat-number">
                    <?php 
                    $today_logins_sql = "SELECT COUNT(*) as count FROM users WHERE DATE(last_login) = CURDATE()";
                    $result = $conn->query($today_logins_sql);
                    echo $result->fetch_assoc()['count'];
                    ?>
                </span>
                <span class="stat-label">Today's Logins</span>
            </div>
            <div class="login-stat">
                <span class="stat-number">
                    <?php 
                    $week_logins_sql = "SELECT COUNT(*) as count FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                    $result = $conn->query($week_logins_sql);
                    echo $result->fetch_assoc()['count'];
                    ?>
                </span>
                <span class="stat-label">This Week</span>
            </div>
        </div>
        <canvas id="loginChart" height="200"></canvas>
    </div>
</div>

                        <div class="content-row">
                            <div class="content-card">
                                <h3>User Distribution</h3>
                                <canvas id="userDistributionChart"></canvas>
                            </div>
                            
                            <div class="content-card">
                                <h3>System Status</h3>
                                <div class="system-status">
                                    <div class="status-item">
                                        <span class="status-label">Database</span>
                                        <span class="status-indicator online">Online</span>
                                    </div>
                                    <div class="status-item">
                                        <span class="status-label">API Services</span>
                                        <span class="status-indicator online">Online</span>
                                    </div>
                                    <div class="status-item">
                                        <span class="status-label">Payment Gateway</span>
                                        <span class="status-indicator online">Online</span>
                                    </div>
                                    <div class="status-item">
                                        <span class="status-label">Mobile App</span>
                                        <span class="status-indicator online">Online</span>
                                    </div>
                                    <div class="status-item">
                                        <span class="status-label">Backup System</span>
                                        <span class="status-indicator warning">Scheduled</span>
                                    </div>
                                </div>
                                <div class="system-metrics">
                                    <div class="metric">
                                        <span class="metric-label">Server Load</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 42%"></div>
                                        </div>
                                        <span class="metric-value">42%</span>
                                    </div>
                                    <div class="metric">
                                        <span class="metric-label">Storage Usage</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 68%"></div>
                                        </div>
                                        <span class="metric-value">68%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management Page -->
                <div id="userManagementPage" class="page">
                    <div class="page-header">
                        <h2>User Management</h2>
                        <div class="header-actions">
                            <button id="addUserBtn" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                            <button class="btn btn-outline">
                                <i class="fas fa-download"></i> Export Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="filters-bar">
                        <div class="filter-group">
                            <label for="userRoleFilter">Role:</label>
                            <select id="userRoleFilter">
                                <option value="">All Roles</option>
                                <option value="admin">Administrator</option>
                                <option value="farmer">Farmer</option>
                                <option value="supporter">Supporter</option>
                                <option value="researcher">Researcher</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="userStatusFilter">Status:</label>
                            <select id="userStatusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <input type="text" id="userSearch" placeholder="Search users...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Join Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <?php
                                $users = getUsers($conn);
                                foreach ($users as $user):
                                ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                                    <td><span class="status-badge status-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-icon delete-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination">
                        <button class="btn btn-outline" id="prevPage">Previous</button>
                        <span class="page-info">Page 1 of 5</span>
                        <button class="btn btn-outline" id="nextPage">Next</button>
                    </div>
                </div>

                <!-- Farmers Management Page -->
                <div id="farmersPage" class="page">
                    <div class="page-header">
                        <h2>Farmer Management</h2>
                        <div class="header-actions">
                            <button id="addFarmerBtn" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Register Farmer
                            </button>
                            <button class="btn btn-outline">
                                <i class="fas fa-download"></i> Export Data
                            </button>
                        </div>
                    </div>
                    
                    <div class="filters-bar">
                        <div class="filter-group">
                            <label for="farmerRegionFilter">Region:</label>
                            <select id="farmerRegionFilter">
                                <option value="">All Regions</option>
                                <option value="luzon">Luzon</option>
                                <option value="visayas">Visayas</option>
                                <option value="mindanao">Mindanao</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="farmerYieldFilter">Yield Level:</label>
                            <select id="farmerYieldFilter">
                                <option value="">All Levels</option>
                                <option value="high">High (100+ cavans/ha)</option>
                                <option value="medium">Medium (70-99 cavans/ha)</option>
                                <option value="low">Low (below 70 cavans/ha)</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <input type="text" id="farmerSearch" placeholder="Search farmers...">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    
                    <div class="farmers-grid" id="farmersGrid">
                        <?php
                        $farmers = getFarmers($conn);
                        foreach ($farmers as $farmer):
                        ?>
                        <div class="farmer-card">
                            <div class="farmer-header">
                                <div class="farmer-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="farmer-info">
                                    <h4><?php echo htmlspecialchars($farmer['full_name']); ?></h4>
                                    <span class="farmer-location"><?php echo htmlspecialchars($farmer['location']); ?></span>
                                </div>
                            </div>
                            <div class="farmer-details">
                                <div class="farmer-stat">
                                    <span class="stat-label">Experience</span>
                                    <span class="stat-value"><?php echo $farmer['experience_years']; ?> years</span>
                                </div>
                                <div class="farmer-stat">
                                    <span class="stat-label">Farm Size</span>
                                    <span class="stat-value"><?php echo $farmer['farm_size']; ?> ha</span>
                                </div>
                                <div class="farmer-stat">
                                    <span class="stat-label">Method</span>
                                    <span class="stat-value"><?php echo ucfirst($farmer['farming_method']); ?></span>
                                </div>
                            </div>
                            <div class="farmer-actions">
                                <button class="btn btn-outline btn-sm">View Details</button>
                                <button class="btn btn-primary btn-sm">Edit</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Campaigns Management Page -->
                <div id="campaignsPage" class="page">
                    <div class="page-header">
                        <h2>Campaign Management</h2>
                        <div class="header-actions">
                            <button id="createCampaignBtn" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Campaign
                            </button>
                            <button class="btn btn-outline">
                                <i class="fas fa-chart-bar"></i> Campaign Analytics
                            </button>
                        </div>
                    </div>
                    
                    <div class="campaigns-overview">
                        <div class="campaign-stat">
                            <div class="stat-value">₱<?php echo number_format($stats['total_funding'], 2); ?></div>
                            <div class="stat-label">Total Funding</div>
                        </div>
                        <div class="campaign-stat">
                            <div class="stat-value"><?php echo $stats['active_campaigns']; ?></div>
                            <div class="stat-label">Active Campaigns</div>
                        </div>
                        <div class="campaign-stat">
                            <div class="stat-value">89%</div>
                            <div class="stat-label">Success Rate</div>
                        </div>
                        <div class="campaign-stat">
                            <div class="stat-value">2,847</div>
                            <div class="stat-label">Total Supporters</div>
                        </div>
                    </div>
                    
                    <div class="campaigns-grid" id="campaignsGrid">
                        <?php
                        $campaigns = getCampaigns($conn);
                        foreach ($campaigns as $campaign):
                            $progress = ($campaign['current_amount'] / $campaign['goal_amount']) * 100;
                        ?>
                        <div class="campaign-card">
                            <div class="campaign-header">
                                <h4><?php echo htmlspecialchars($campaign['title']); ?></h4>
                                <span class="campaign-type"><?php echo ucfirst($campaign['type']); ?></span>
                            </div>
                            <div class="campaign-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min($progress, 100); ?>%"></div>
                                </div>
                                <div class="progress-stats">
                                    <span>₱<?php echo number_format($campaign['current_amount'], 2); ?></span>
                                    <span>₱<?php echo number_format($campaign['goal_amount'], 2); ?></span>
                                </div>
                            </div>
                            <div class="campaign-details">
                                <div class="campaign-deadline">
                                    <i class="fas fa-calendar"></i>
                                    Ends: <?php echo date('M j, Y', strtotime($campaign['deadline'])); ?>
                                </div>
                                <div class="campaign-status status-<?php echo $campaign['status']; ?>">
                                    <?php echo ucfirst($campaign['status']); ?>
                                </div>
                            </div>
                            <div class="campaign-actions">
                                <button class="btn btn-outline btn-sm">View</button>
                                <button class="btn btn-primary btn-sm">Edit</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Analytics Page -->
                <div id="analyticsPage" class="page">
                    <div class="page-header">
                        <h2>Platform Analytics</h2>
                        <div class="header-actions">
                            <select id="analyticsTimeframe">
                                <option value="7days">Last 7 Days</option>
                                <option value="30days" selected>Last 30 Days</option>
                                <option value="90days">Last 90 Days</option>
                                <option value="1year">Last Year</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="analytics-content">
                        <div class="analytics-row">
                            <div class="analytics-card large">
                                <h3>User Growth</h3>
                                <canvas id="userGrowthChart"></canvas>
                            </div>
                            <div class="analytics-card">
                                <h3>Platform Metrics</h3>
                                <div class="metrics-grid">
                                    <div class="metric-card">
                                        <div class="metric-value">68%</div>
                                        <div class="metric-label">User Retention</div>
                                    </div>
                                    <div class="metric-card">
                                        <div class="metric-value">24.5</div>
                                        <div class="metric-label">Avg Session (min)</div>
                                    </div>
                                    <div class="metric-card">
                                        <div class="metric-value">3.2</div>
                                        <div class="metric-label">Pages/Session</div>
                                    </div>
                                    <div class="metric-card">
                                        <div class="metric-value">42%</div>
                                        <div class="metric-label">Mobile Users</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="analytics-row">
                            <div class="analytics-card">
                                <h3>Regional Distribution</h3>
                                <canvas id="regionalDistributionChart"></canvas>
                            </div>
                            <div class="analytics-card">
                                <h3>Campaign Performance</h3>
                                <canvas id="campaignPerformanceChart"></canvas>
                            </div>
                            <div class="analytics-card">
                                <h3>Yield Improvements</h3>
                                <canvas id="yieldImprovementChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Settings Page -->
                <div id="systemPage" class="page">
                    <div class="page-header">
                        <h2>System Settings</h2>
                    </div>
                    
                    <div class="settings-content">
                        <div class="settings-section">
                            <h3>General Settings</h3>
                            <div class="settings-grid">
                                <div class="setting-item">
                                    <label for="siteName">Site Name</label>
                                    <input type="text" id="siteName" value="FarmStat">
                                </div>
                                <div class="setting-item">
                                    <label for="adminEmail">Admin Email</label>
                                    <input type="email" id="adminEmail" value="admin@farmstat.ph">
                                </div>
                                <div class="setting-item">
                                    <label for="timezone">Timezone</label>
                                    <select id="timezone">
                                        <option value="Asia/Manila" selected>Asia/Manila (UTC+8)</option>
                                        <option value="UTC">UTC</option>
                                    </select>
                                </div>
                                <div class="setting-item">
                                    <label for="maintenanceMode">Maintenance Mode</label>
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="maintenanceMode">
                                        <span class="slider"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h3>Security Settings</h3>
                            <div class="settings-grid">
                                <div class="setting-item">
                                    <label for="passwordPolicy">Password Policy</label>
                                    <select id="passwordPolicy">
                                        <option value="standard">Standard (8+ characters)</option>
                                        <option value="strong" selected>Strong (12+ characters with symbols)</option>
                                    </select>
                                </div>
                                <div class="setting-item">
                                    <label for="sessionTimeout">Session Timeout</label>
                                    <select id="sessionTimeout">
                                        <option value="30">30 minutes</option>
                                        <option value="60" selected>60 minutes</option>
                                        <option value="120">2 hours</option>
                                    </select>
                                </div>
                                <div class="setting-item">
                                    <label for="twoFactorAuth">Two-Factor Authentication</label>
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="twoFactorAuth" checked>
                                        <span class="slider"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h3>Backup & Maintenance</h3>
                            <div class="settings-grid">
                                <div class="setting-item">
                                    <label for="backupFrequency">Backup Frequency</label>
                                    <select id="backupFrequency">
                                        <option value="daily">Daily</option>
                                        <option value="weekly" selected>Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                <div class="setting-item">
                                    <label for="backupRetention">Backup Retention</label>
                                    <select id="backupRetention">
                                        <option value="7">7 days</option>
                                        <option value="30" selected>30 days</option>
                                        <option value="90">90 days</option>
                                    </select>
                                </div>
                                <div class="setting-item full-width">
                                    <button class="btn btn-outline">
                                        <i class="fas fa-download"></i> Download Latest Backup
                                    </button>
                                    <button class="btn btn-primary">
                                        <i class="fas fa-sync"></i> Run Backup Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-actions">
                            <button class="btn btn-outline">Reset to Defaults</button>
                            <button class="btn btn-primary">Save Settings</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

 <!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New User</h2>
        <form id="addUserForm">
            <div class="form-group">
                <label for="userFullName">Full Name *</label>
                <input type="text" id="userFullName" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="userEmail">Email *</label>
                <input type="email" id="userEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="userRole">Role *</label>
                <select id="userRole" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Administrator</option>
                    <option value="farmer">Farmer</option>
                    <option value="supporter">Supporter</option>
                    <option value="researcher">Researcher</option>
                </select>
            </div>
            <div class="form-group">
                <label for="userPassword">Password *</label>
                <input type="password" id="userPassword" name="password" required minlength="6">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" id="cancelAddUser">Cancel</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

    <!-- Add Farmer Modal -->
    <div id="addFarmerModal" class="modal">
        <div class="modal-content large">
            <span class="close">&times;</span>
            <h2>Register New Rice Farmer</h2>
            <form id="addFarmerForm" action="add_farmer.php" method="POST">
                <div class="form-section">
                    <h3>Personal Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="farmerName">Full Name</label>
                            <input type="text" id="farmerName" name="full_name" placeholder="e.g. Juan Dela Cruz" required>
                        </div>
                        <div class="form-group">
                            <label for="farmerExperience">Years Farming Experience</label>
                            <input type="number" id="farmerExperience" name="experience_years" placeholder="e.g. 15" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="farmerLocation">Farm Location</label>
                            <input type="text" id="farmerLocation" name="location" placeholder="e.g. Nueva Ecija" required>
                        </div>
                        <div class="form-group">
                            <label for="farmSize">Total Farm Size (hectares)</label>
                            <input type="number" id="farmSize" name="farm_size" placeholder="e.g. 5.2" step="0.1" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Rice Farming Details</h3>
                    <div class="form-group">
                        <label for="riceVarieties">Primary Rice Varieties</label>
                        <select id="riceVarieties" name="rice_varieties[]" multiple>
                            <option value="jasmine">Jasmine</option>
                            <option value="sinandomeng">Sinandomeng</option>
                            <option value="ir64">IR64</option>
                            <option value="rc218">RC218</option>
                            <option value="nsic">NSIC Rc222</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="farmingMethod">Farming Method</label>
                            <select id="farmingMethod" name="farming_method" required>
                                <option value="">Select Method</option>
                                <option value="traditional">Traditional</option>
                                <option value="modern">Modern</option>
                                <option value="organic">Organic</option>
                                <option value="hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="landOwnership">Land Ownership</label>
                            <select id="landOwnership" name="land_ownership" required>
                                <option value="">Select Type</option>
                                <option value="owned">Owned</option>
                                <option value="leased">Leased</option>
                                <option value="ancestral">Ancestral</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancelAddFarmer">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Farmer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    <div id="createCampaignModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create Support Campaign</h2>
            <form id="createCampaignForm" action="create_campaign.php" method="POST">
                <div class="form-group">
                    <label for="campaignTitle">Campaign Title</label>
                    <input type="text" id="campaignTitle" name="title" placeholder="e.g. Modern Irrigation System for Rice Farm" required>
                </div>
                <div class="form-group">
                    <label for="campaignType">Campaign Type</label>
                    <select id="campaignType" name="type" required>
                        <option value="">Select Type</option>
                        <option value="seeds">Seeds & Inputs</option>
                        <option value="equipment">Equipment Modernization</option>
                        <option value="infrastructure">Infrastructure</option>
                        <option value="training">Knowledge & Training</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="campaignGoal">Funding Goal (₱)</label>
                    <input type="number" id="campaignGoal" name="goal_amount" placeholder="e.g. 75000" required>
                </div>
                <div class="form-group">
                    <label for="campaignDescription">Campaign Description</label>
                    <textarea id="campaignDescription" name="description" rows="4" placeholder="Describe how this funding will improve rice farming operations and expected impact..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="campaignDeadline">Campaign Deadline</label>
                    <input type="date" id="campaignDeadline" name="deadline" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancelCampaign">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="adminjava.js"></script>
</body>
</html>

<?php
// Include helper functions
include 'helpers.php';
?>