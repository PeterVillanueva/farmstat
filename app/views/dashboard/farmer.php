<?php
$title = 'Farmer Dashboard - FarmStats';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <span class="logo">🌾</span>
                <span>FarmStat</span>
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
                    <span>Campaigns</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-tractor"></i>
                    <span>Farmers</span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['name'] ?? 'User'); ?>!</h1>
            <div class="admin-menu">
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="/logout" class="btn btn-outline">Logout</a>
            </div>
        </header>

        <div class="content-row">
            <div class="content-card">
                <h3>Recent Campaigns</h3>
                <div class="campaigns-list">
                    <?php if (!empty($campaigns)): ?>
                        <?php foreach ($campaigns as $campaign): ?>
                            <div class="campaign-item">
                                <h4><?php echo htmlspecialchars($campaign['title']); ?></h4>
                                <p><?php echo htmlspecialchars($campaign['description']); ?></p>
                                <span class="campaign-goal">Goal: ₱<?php echo number_format($campaign['funding_goal'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No campaigns found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>

