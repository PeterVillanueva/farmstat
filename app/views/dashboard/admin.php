<?php
$title = 'Admin Dashboard - FarmStats';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <span class="logo">🌾</span>
                <span>FarmStat Admin</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item active">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>User Management</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-tractor"></i>
                    <span>Farmers</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>Campaigns</span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <div class="admin-menu">
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="/logout" class="btn btn-outline">Logout</a>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <span class="stat-number"><?php echo $stats['total_users']; ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tractor"></i></div>
                <div class="stat-content">
                    <span class="stat-number"><?php echo $stats['total_farmers']; ?></span>
                    <span class="stat-label">Total Farmers</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stat-content">
                    <span class="stat-number">₱<?php echo number_format($stats['total_funding'], 2); ?></span>
                    <span class="stat-label">Total Funding</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                <div class="stat-content">
                    <span class="stat-number"><?php echo $stats['active_campaigns']; ?></span>
                    <span class="stat-label">Active Campaigns</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>

