<?php
session_start();
include 'database.php';
include 'check_auth.php';

// Ensure only farmers can access this dashboard
if ($_SESSION['user_role'] !== 'farmer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get farmer's data
$user_sql = "SELECT * FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();

// Get farmer's campaigns count
$campaigns_sql = "SELECT COUNT(*) as campaign_count FROM campaigns WHERE farmer_id = ?";
$campaigns_stmt = $conn->prepare($campaigns_sql);
$campaigns_stmt->bind_param("i", $user_id);
$campaigns_stmt->execute();
$campaigns_data = $campaigns_stmt->get_result()->fetch_assoc();

// Update last activity
$update_sql = "UPDATE users SET last_activity = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - FarmStats</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1a4d2e;
            --primary-medium: #4a7c59;
            --primary-light: #8db596;
            --accent-gold: #d4af37;
            --accent-orange: #ff9a3d;
            --background: #f5f9f7;
            --white: #ffffff;
            --text: #2d3a2d;
            --text-light: #5a6c5a;
            --border: #dde8e0;
            --shadow: 0 4px 12px rgba(26, 77, 46, 0.08);
            --shadow-lg: 0 8px 24px rgba(26, 77, 46, 0.12);
            --border-radius: 15px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: var(--primary-dark);
            color: white;
            padding: 1.5rem 0;
        }

        .sidebar-brand {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand .logo {
            font-size: 2rem;
            margin-right: 0.5rem;
        }

        .sidebar-nav ul {
            list-style: none;
        }

        .sidebar-nav li {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav li:hover,
        .sidebar-nav li.active {
            background: rgba(255,255,255,0.1);
            border-left-color: var(--accent-gold);
        }

        .sidebar-nav i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
        }

        .dashboard-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .header-left h1 {
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .season-badge {
            background: var(--accent-orange);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-medium);
        }

        .stat-card.warning {
            border-left-color: var(--accent-orange);
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.info {
            border-left-color: #17a2b8;
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--primary-medium);
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-dark);
            display: block;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-btn {
            background: white;
            border: 2px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .quick-action-btn:hover {
            border-color: var(--primary-medium);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .action-icon {
            font-size: 2rem;
            color: var(--primary-medium);
            margin-bottom: 0.5rem;
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .content-card h3 {
            color: var(--primary-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-medium);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-medium);
            color: var(--primary-medium);
        }

        .btn-outline:hover {
            background: var(--primary-medium);
            color: white;
        }

        /* Activity List */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary-medium);
        }

        .progress-bar {
            background: var(--border);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .progress-fill {
            background: var(--primary-medium);
            height: 100%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <span class="logo">🌾</span>
                <span>FarmStats</span>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>My Dashboard</span>
                    </li>
                    <li>
                        <i class="fas fa-tractor"></i>
                        <span>My Fields</span>
                    </li>
                    <li>
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>My Campaigns</span>
                    </li>
                    <li>
                        <i class="fas fa-calendar-alt"></i>
                        <span>Season Tracking</span>
                    </li>
                    <li>
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </li>
                    <li>
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <h1>Welcome, <?php echo $_SESSION['user_name']; ?>! 👨‍🌾</h1>
                    <div class="season-indicator">
                        <span class="season-badge">2024 Dry Season - Active Farmer</span>
                    </div>
                </div>
                <div class="header-right">
                    <div class="user-menu">
                        <div class="user-info">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?php echo $_SESSION['user_name']; ?></span>
                        </div>
                        <a href="logout.php" class="btn btn-outline">Logout</a>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tractor"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">0</span>
                        <span class="stat-label">Rice Fields</span>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $campaigns_data['campaign_count']; ?></span>
                        <span class="stat-label">Active Campaigns</span>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">68%</span>
                        <span class="stat-label">Season Progress</span>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">₱0</span>
                        <span class="stat-label">Funding Received</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="add-field.php" class="quick-action-btn">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h4>Add New Field</h4>
                    <p>Register your rice field</p>
                </a>
                <a href="create-campaign.php" class="quick-action-btn">
                    <div class="action-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4>Start Campaign</h4>
                    <p>Seek community support</p>
                </a>
                <a href="update-progress.php" class="quick-action-btn">
                    <div class="action-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h4>Update Progress</h4>
                    <p>Record field updates</p>
                </a>
                <a href="reports.php" class="quick-action-btn">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4>View Reports</h4>
                    <p>Analytics & insights</p>
                </a>
            </div>

            <!-- Recent Activity & Campaigns -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="content-card">
                    <h3>My Active Campaigns</h3>
                    <?php
                    $active_campaigns_sql = "SELECT * FROM campaigns WHERE farmer_id = ? AND status = 'active' LIMIT 3";
                    $active_stmt = $conn->prepare($active_campaigns_sql);
                    $active_stmt->bind_param("i", $user_id);
                    $active_stmt->execute();
                    $campaigns = $active_stmt->get_result();
                    
                    if ($campaigns->num_rows > 0): 
                        while($campaign = $campaigns->fetch_assoc()): 
                    ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="activity-content">
                                <h4><?php echo htmlspecialchars($campaign['title']); ?></h4>
                                <p>Goal: ₱<?php echo number_format($campaign['goal_amount'], 2); ?></p>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($campaign['amount_raised'] / $campaign['goal_amount']) * 100; ?>%"></div>
                                </div>
                                <small>₱<?php echo number_format($campaign['amount_raised'], 2); ?> raised</small>
                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <p>No active campaigns. <a href="create-campaign.php">Start your first campaign!</a></p>
                    <?php endif; ?>
                </div>

                <div class="content-card">
                    <h3>Recent Activity</h3>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Logged in to Farmer Dashboard</strong></p>
                                <small>Just now</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Farmer profile active</strong></p>
                                <small>Member since <?php echo date('M j, Y', strtotime($user_data['created_at'])); ?></small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Welcome to FarmStats!</strong></p>
                                <small>Start by adding your rice fields</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Getting Started Guide -->
            <div class="content-card">
                <h3>Getting Started Guide</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">1</div>
                        <h4>Add Your Fields</h4>
                        <p>Register your rice fields to start tracking</p>
                    </div>
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">2</div>
                        <h4>Create Campaign</h4>
                        <p>Seek community support for your farming needs</p>
                    </div>
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">3</div>
                        <h4>Track Progress</h4>
                        <p>Update your field progress regularly</p>
                    </div>
                    <div style="text-align: center; padding: 1rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">4</div>
                        <h4>Engage Community</h4>
                        <p>Share updates and connect with supporters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple navigation
        document.querySelectorAll('.sidebar-nav li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-nav li').forEach(li => li.classList.remove('active'));
                this.classList.add('active');
                
                // You can add page switching logic here
                const pageName = this.querySelector('span').textContent.toLowerCase().replace(' ', '');
                alert('Navigating to: ' + pageName); // Replace with actual navigation
            });
        });
    </script>
</body>
</html>