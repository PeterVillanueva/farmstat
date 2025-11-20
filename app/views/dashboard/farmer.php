<?php
$title = 'Farmer Dashboard - FarmStats';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div id="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <span class="logo">🌾</span>
                <span>FarmStat</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item active" data-page="overview">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </li>
                <li class="nav-item" data-page="farmers">
                    <i class="fas fa-users"></i>
                    <span>Farmers</span>
                </li>
                <li class="nav-item" data-page="riceMonitoring">
                    <i class="fas fa-seedling"></i>
                    <span>Rice Monitoring</span>
                </li>
                <li class="nav-item" data-page="seasonalTracking">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Seasonal Tracking</span>
                </li>
                <li class="nav-item" data-page="livestock">
                    <i class="fas fa-paw"></i>
                    <span>Livestock</span>
                </li>
                <li class="nav-item" data-page="crowdfunding">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>Crowdfunding</span>
                </li>
                <li class="nav-item" data-page="analytics">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </li>
                <li class="nav-item" data-page="community">
                    <i class="fas fa-users"></i>
                    <span>Community</span>
                </li>
                <li class="nav-item" data-page="profile">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile</span>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <h1 id="pageTitle">Rice Farming Intelligence Dashboard</h1>
                <div class="season-indicator">
                    <span class="season-badge">2024 Dry Season</span>
                    <span class="progress-text">68% Complete</span>
                </div>
            </div>
            <div class="header-right">
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar" id="userAvatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span id="userWelcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
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
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tractor"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-number" id="totalFarmers"><?php echo count($farmers ?? []); ?></span>
                            <span class="stat-label">Rice Farmers</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-number" id="activeCrops">68</span>
                            <span class="stat-label">Active Rice Fields</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-number" id="totalFunding">₱18.2M</span>
                            <span class="stat-label">Community Funding</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-number" id="yieldIncrease">156%</span>
                            <span class="stat-label">Avg Yield Increase</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-content">
                    <div class="content-row">
                        <div class="content-card large">
                            <h3>Rice Season 2024 Progress</h3>
                            <div class="season-phase-tracker">
                                <div class="phase active">
                                    <div class="phase-icon">🌱</div>
                                    <div class="phase-info">
                                        <h4>Planting Phase</h4>
                                        <p>92% Complete</p>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 92%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="phase active">
                                    <div class="phase-icon">🌿</div>
                                    <div class="phase-info">
                                        <h4>Growth Phase</h4>
                                        <p>68% Complete</p>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 68%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="phase">
                                    <div class="phase-icon">🌾</div>
                                    <div class="phase-info">
                                        <h4>Harvest Phase</h4>
                                        <p>Starts August 2024</p>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="content-card">
                            <h3>Recent Campaign Activities</h3>
                            <div class="activity-list">
                                <?php if (!empty($campaigns)): ?>
                                    <?php foreach (array_slice($campaigns, 0, 5) as $campaign): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="fas fa-hand-holding-heart"></i>
                                            </div>
                                            <div class="activity-content">
                                                <h4><?php echo htmlspecialchars($campaign['title']); ?></h4>
                                                <p><?php echo htmlspecialchars(substr($campaign['description'], 0, 80)); ?>...</p>
                                                <span class="activity-meta">Goal: ₱<?php echo number_format($campaign['funding_goal'], 2); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p>No recent campaign activities.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="content-row">
                        <div class="content-card">
                            <h3>My Farm Profile</h3>
                            <div class="farm-profile">
                                <div class="profile-item">
                                    <span class="profile-label">Farmer Name:</span>
                                    <span class="profile-value"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Not set'); ?></span>
                                </div>
                                <div class="profile-item">
                                    <span class="profile-label">Farm Size:</span>
                                    <span class="profile-value">3.2 hectares</span>
                                </div>
                                <div class="profile-item">
                                    <span class="profile-label">Primary Variety:</span>
                                    <span class="profile-value">Jasmine Rice</span>
                                </div>
                                <div class="profile-item">
                                    <span class="profile-label">Farming Experience:</span>
                                    <span class="profile-value">15 years</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="content-card">
                            <h3>Weather & Season Info</h3>
                            <div class="weather-info">
                                <div class="weather-item">
                                    <i class="fas fa-sun"></i>
                                    <span>Today: Sunny, 29°C</span>
                                </div>
                                <div class="weather-item">
                                    <i class="fas fa-tint"></i>
                                    <span>Rainfall: 85mm this month</span>
                                </div>
                                <div class="weather-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Days to Harvest: 32</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placeholder pages for navigation -->
            <div id="farmersPage" class="page">
                <h2>Farmer Management</h2>
                <p>Farmer management functionality will be implemented here.</p>
            </div>

            <div id="riceMonitoringPage" class="page">
                <h2>Rice Monitoring</h2>
                <p>Rice monitoring functionality will be implemented here.</p>
            </div>

            <div id="seasonalTrackingPage" class="page">
                <h2>Seasonal Tracking</h2>
                <p>Seasonal tracking functionality will be implemented here.</p>
            </div>

            <div id="livestockPage" class="page">
                <h2>Livestock Management</h2>
                <p>Livestock management functionality will be implemented here.</p>
            </div>

            <div id="crowdfundingPage" class="page">
                <h2>Crowdfunding</h2>
                <p>Crowdfunding functionality will be implemented here.</p>
            </div>

            <div id="analyticsPage" class="page">
                <h2>Analytics</h2>
                <p>Analytics functionality will be implemented here.</p>
            </div>

            <div id="communityPage" class="page">
                <h2>Community</h2>
                <p>Community functionality will be implemented here.</p>
            </div>

            <div id="profilePage" class="page">
                <h2>Profile Settings</h2>
                <p>Profile settings functionality will be implemented here.</p>
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

    // Page titles mapping
    const pageTitles = {
        overview: 'Rice Farming Intelligence Dashboard',
        farmers: 'Farmer Management',
        riceMonitoring: 'Rice Monitoring System',
        seasonalTracking: 'Seasonal Tracking Analytics',
        livestock: 'Livestock Integration Management',
        crowdfunding: 'Community Support & Funding',
        analytics: 'Advanced Analytics & Insights',
        community: 'Farmer Community & Knowledge Sharing',
        profile: 'Profile & Settings'
    };

    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            
            // Remove active class from all nav items and pages
            navItems.forEach(nav => nav.classList.remove('active'));
            pages.forEach(page => page.classList.remove('active'));
            
            // Add active class to clicked nav item and corresponding page
            this.classList.add('active');
            const targetPageElement = document.getElementById(targetPage + 'Page');
            if (targetPageElement) {
                targetPageElement.classList.add('active');
                // Update page title
                if (pageTitle && pageTitles[targetPage]) {
                    pageTitle.textContent = pageTitles[targetPage];
                }
            }
        });
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>