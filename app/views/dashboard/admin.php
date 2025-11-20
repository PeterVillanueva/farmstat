<?php
$title = 'Admin Dashboard - FarmStats';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div id="adminDashboard">
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
                <li class="nav-item" data-page="users">
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
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <h1 id="pageTitle">Admin Dashboard</h1>
                <div class="breadcrumb">
                    <span class="breadcrumb-item">Admin</span>
                    <span class="breadcrumb-separator">></span>
                    <span class="breadcrumb-item active" id="pageBreadcrumb">Dashboard</span>
                </div>
            </div>
            <div class="header-right">
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/logout" class="btn btn-outline">Logout</a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            <!-- Overview Page -->
            <div id="overviewPage" class="page active">
                <div class="stats-grid">
                    <div class="stat-card" data-page="users">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['total_users']; ?></span>
                            <span class="stat-label">Total Users</span>
                        </div>
                        <div class="stat-arrow"><i class="fas fa-arrow-right"></i></div>
                    </div>
                    <div class="stat-card" data-page="farmers">
                        <div class="stat-icon"><i class="fas fa-tractor"></i></div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['total_farmers']; ?></span>
                            <span class="stat-label">Total Farmers</span>
                        </div>
                        <div class="stat-arrow"><i class="fas fa-arrow-right"></i></div>
                    </div>
                    <div class="stat-card" data-page="campaigns">
                        <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                        <div class="stat-content">
                            <span class="stat-number">₱<?php echo number_format($stats['total_funding'], 2); ?></span>
                            <span class="stat-label">Total Funding</span>
                        </div>
                        <div class="stat-arrow"><i class="fas fa-arrow-right"></i></div>
                    </div>
                    <div class="stat-card" data-page="campaigns">
                        <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo $stats['active_campaigns']; ?></span>
                            <span class="stat-label">Active Campaigns</span>
                        </div>
                        <div class="stat-arrow"><i class="fas fa-arrow-right"></i></div>
                    </div>
                </div>

                <div class="dashboard-content">
                    <div class="content-row">
                        <div class="content-card large">
                            <h3>System Overview</h3>
                            <div class="overview-metrics">
                                <div class="metric">
                                    <div class="metric-icon">📊</div>
                                    <div class="metric-info">
                                        <h4>Platform Performance</h4>
                                        <p>System running smoothly with <?php echo $stats['total_users']; ?> active users</p>
                                    </div>
                                </div>
                                <div class="metric">
                                    <div class="metric-icon">🌾</div>
                                    <div class="metric-info">
                                        <h4>Agricultural Impact</h4>
                                        <p><?php echo $stats['total_farmers']; ?> farmers supported across the platform</p>
                                    </div>
                                </div>
                                <div class="metric">
                                    <div class="metric-icon">💰</div>
                                    <div class="metric-info">
                                        <h4>Community Funding</h4>
                                        <p>₱<?php echo number_format($stats['total_funding'], 2); ?> raised through crowdfunding</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="content-card">
                            <h3>Quick Actions</h3>
                            <div class="action-list">
                                <button class="action-btn" data-page="users">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Manage Users</span>
                                </button>
                                <button class="action-btn" data-page="farmers">
                                    <i class="fas fa-tractor"></i>
                                    <span>View Farmers</span>
                                </button>
                                <button class="action-btn" data-page="campaigns">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Review Campaigns</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Page -->
            <div id="usersPage" class="page">
                <div class="page-header">
                    <h2>User Management</h2>
                    <div class="page-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Add New User
                        </button>
                    </div>
                </div>
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading user data...</p>
                </div>
            </div>

            <!-- Farmers Page -->
            <div id="farmersPage" class="page">
                <div class="page-header">
                    <h2>Farmer Management</h2>
                    <div class="page-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add New Farmer
                        </button>
                    </div>
                </div>
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading farmer data...</p>
                </div>
            </div>

            <!-- Campaigns Page -->
            <div id="campaignsPage" class="page">
                <div class="page-header">
                    <h2>Campaign Management</h2>
                    <div class="page-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Create Campaign
                        </button>
                    </div>
                </div>
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading campaign data...</p>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigation functionality
    const navItems = document.querySelectorAll('.nav-item');
    const pages = document.querySelectorAll('.page');
    const pageTitle = document.getElementById('pageTitle');
    const pageBreadcrumb = document.getElementById('pageBreadcrumb');
    const statCards = document.querySelectorAll('.stat-card[data-page]');
    const actionBtns = document.querySelectorAll('.action-btn[data-page]');

    // Page titles mapping
    const pageTitles = {
        overview: 'Admin Dashboard',
        users: 'User Management',
        farmers: 'Farmer Management',
        campaigns: 'Campaign Management'
    };

    function switchToPage(targetPage) {
        // Remove active class from all nav items and pages
        navItems.forEach(nav => nav.classList.remove('active'));
        pages.forEach(page => page.classList.remove('active'));
        
        // Add active class to corresponding nav item and page
        const targetNav = document.querySelector(`.nav-item[data-page="${targetPage}"]`);
        const targetPageElement = document.getElementById(targetPage + 'Page');
        
        if (targetNav) targetNav.classList.add('active');
        if (targetPageElement) {
            targetPageElement.classList.add('active');
            
            // Update page title and breadcrumb
            if (pageTitle && pageTitles[targetPage]) {
                pageTitle.textContent = pageTitles[targetPage];
                pageBreadcrumb.textContent = pageTitles[targetPage].replace('Admin ', '');
            }
            
            // Load content if needed
            if (targetPage !== 'overview' && !targetPageElement.classList.contains('loaded')) {
                loadPageContent(targetPage);
            }
        }
    }

    // Navigation item clicks
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            switchToPage(targetPage);
        });
    });

    // Stat card clicks
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            switchToPage(targetPage);
        });
        
        // Add hover effect
        card.style.cursor = 'pointer';
    });

    // Action button clicks
    actionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            switchToPage(targetPage);
        });
    });

    // Load page content via AJAX
    function loadPageContent(page) {
        const pageElement = document.getElementById(page + 'Page');
        const loadingContainer = pageElement.querySelector('.loading-container');
        
        // Show loading state
        if (loadingContainer) {
            loadingContainer.style.display = 'flex';
        }
        
        // Simulate loading for now (will be replaced with actual AJAX calls)
        setTimeout(() => {
            if (loadingContainer) {
                loadingContainer.innerHTML = '<p>Content for ' + pageTitles[page] + ' will be loaded here.</p>';
            }
            pageElement.classList.add('loaded');
        }, 1000);
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>