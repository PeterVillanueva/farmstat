// Admin Dashboard JavaScript - Complete Version
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the dashboard
    initDashboard();
    
    // Navigation functionality
    initNavigation();
    
    // Modal functionality
    initModals();
    
    // Initialize charts with fixes
    safeInitCharts();
    
    // Load sample data
    loadSampleData();
    
    // Initialize filters and search
    initFilters();
    
    // Initialize delete functionality
    initDeleteHandlers();
    
    // Initialize login chart
    initLoginChart();
});

// Initialize dashboard
function initDashboard() {
    console.log('FarmStat Admin Dashboard initialized');
    
    // Set current date in header if needed
    const currentDate = new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Display success/error messages from URL parameters
    displayMessages();
}

// Display success/error messages from URL
function displayMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    
    if (success) {
        showNotification(success, 'success');
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (error) {
        showNotification(error, 'error');
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">&times;</button>
    `;
    
    // Add styles if not exists
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 300px;
                max-width: 500px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease;
            }
            .notification-success { background: #10b981; }
            .notification-error { background: #ef4444; }
            .notification-info { background: #3b82f6; }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
                flex: 1;
            }
            .notification-close {
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                padding: 0;
                margin-left: 15px;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
    
    // Close on click
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.remove();
    });
}

// Navigation functionality
function initNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    const pages = document.querySelectorAll('.page');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetPage = this.getAttribute('data-page');
            
            // Update active nav item
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            
            // Show target page
            pages.forEach(page => {
                page.classList.remove('active');
                if (page.id === `${targetPage}Page`) {
                    page.classList.add('active');
                    
                    // Update page title
                    const pageTitle = document.getElementById('pageTitle');
                    pageTitle.textContent = getPageTitle(targetPage);
                    
                    // Load page-specific data
                    loadPageData(targetPage);
                }
            });
        });
    });
    
    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        });
    }
}

// Get page title based on page ID
function getPageTitle(pageId) {
    const titles = {
        'overview': 'Admin Dashboard',
        'userManagement': 'User Management',
        'farmers': 'Farmer Management',
        'campaigns': 'Campaign Management',
        'analytics': 'Platform Analytics',
        'system': 'System Settings'
    };
    
    return titles[pageId] || 'Admin Dashboard';
}

// Modal functionality
function initModals() {
    // Get all modals
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close, .btn-outline[id^="cancel"]');
    
    // Add User Modal
    const addUserBtn = document.getElementById('addUserBtn');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            document.getElementById('addUserModal').style.display = 'flex';
        });
    }
    
    // Add Farmer Modal
    const addFarmerBtn = document.getElementById('addFarmerBtn');
    if (addFarmerBtn) {
        addFarmerBtn.addEventListener('click', function() {
            document.getElementById('addFarmerModal').style.display = 'flex';
        });
    }
    
    // Create Campaign Modal
    const createCampaignBtn = document.getElementById('createCampaignBtn');
    if (createCampaignBtn) {
        createCampaignBtn.addEventListener('click', function() {
            document.getElementById('createCampaignModal').style.display = 'flex';
        });
    }
    
    // Close modals when clicking close button or cancel
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Close modal when clicking outside
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
    
    // Form submissions
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addNewUser();
        });
    }
    
    const addFarmerForm = document.getElementById('addFarmerForm');
    if (addFarmerForm) {
        addFarmerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addNewFarmer();
        });
    }
    
    const createCampaignForm = document.getElementById('createCampaignForm');
    if (createCampaignForm) {
        createCampaignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            createNewCampaign();
        });
    }
}

// Initialize filters and search functionality
function initFilters() {
    // User management filters
    initUserFilters();
    
    // Farmer management filters
    initFarmerFilters();
    
    // Campaign filters
    initCampaignFilters();
}

// User management filters
function initUserFilters() {
    const roleFilter = document.getElementById('userRoleFilter');
    const statusFilter = document.getElementById('userStatusFilter');
    const userSearch = document.getElementById('userSearch');
    
    if (roleFilter) {
        roleFilter.addEventListener('change', filterUsers);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterUsers);
    }
    if (userSearch) {
        userSearch.addEventListener('input', filterUsers);
    }
}

// Farmer management filters
function initFarmerFilters() {
    const regionFilter = document.getElementById('farmerRegionFilter');
    const yieldFilter = document.getElementById('farmerYieldFilter');
    const farmerSearch = document.getElementById('farmerSearch');
    
    if (regionFilter) {
        regionFilter.addEventListener('change', filterFarmers);
    }
    if (yieldFilter) {
        yieldFilter.addEventListener('change', filterFarmers);
    }
    if (farmerSearch) {
        farmerSearch.addEventListener('input', filterFarmers);
    }
}

// Campaign filters
function initCampaignFilters() {
    const analyticsTimeframe = document.getElementById('analyticsTimeframe');
    if (analyticsTimeframe) {
        analyticsTimeframe.addEventListener('change', function() {
            // Reload analytics data based on timeframe
            loadAnalyticsData(this.value);
        });
    }
}

// Filter users
function filterUsers() {
    const roleFilter = document.getElementById('userRoleFilter')?.value || '';
    const statusFilter = document.getElementById('userStatusFilter')?.value || '';
    const searchTerm = document.getElementById('userSearch')?.value.toLowerCase() || '';
    
    const rows = document.querySelectorAll('#usersTableBody tr');
    
    rows.forEach(row => {
        const role = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        const roleMatch = !roleFilter || role.includes(roleFilter);
        const statusMatch = !statusFilter || status.includes(statusFilter);
        const searchMatch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
        
        row.style.display = roleMatch && statusMatch && searchMatch ? '' : 'none';
    });
}

// Filter farmers
function filterFarmers() {
    const regionFilter = document.getElementById('farmerRegionFilter')?.value || '';
    const yieldFilter = document.getElementById('farmerYieldFilter')?.value || '';
    const searchTerm = document.getElementById('farmerSearch')?.value.toLowerCase() || '';
    
    const cards = document.querySelectorAll('.farmer-card');
    
    cards.forEach(card => {
        const location = card.querySelector('.farmer-location, .farmer-basic-info p')?.textContent.toLowerCase() || '';
        const yieldValue = parseInt(card.querySelector('.farmer-stat-value')?.textContent) || 0;
        const name = card.querySelector('h3, h4')?.textContent.toLowerCase() || '';
        
        let regionMatch = true;
        if (regionFilter) {
            regionMatch = location.includes(regionFilter);
        }
        
        let yieldMatch = true;
        if (yieldFilter === 'high') yieldMatch = yieldValue >= 100;
        if (yieldFilter === 'medium') yieldMatch = yieldValue >= 70 && yieldValue <= 99;
        if (yieldFilter === 'low') yieldMatch = yieldValue < 70;
        
        const searchMatch = !searchTerm || name.includes(searchTerm) || location.includes(searchTerm);
        
        card.style.display = regionMatch && yieldMatch && searchMatch ? '' : 'none';
    });
}

// Initialize delete handlers
function initDeleteHandlers() {
    // User delete handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-user') || e.target.closest('.btn-action.delete')) {
            const button = e.target.closest('.delete-user') || e.target.closest('.btn-action.delete');
            const userId = button.getAttribute('data-id');
            const userName = button.closest('tr')?.querySelector('td:nth-child(2)')?.textContent || 'this user';
            
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                window.location.href = `delete_user.php?id=${userId}`;
            }
        }
        
        // Edit user handlers
        if (e.target.closest('.edit-user') || e.target.closest('.btn-action.edit')) {
            const button = e.target.closest('.edit-user') || e.target.closest('.btn-action.edit');
            const userId = button.getAttribute('data-id');
            showEditUserModal(userId);
        }
    });
}

// Show edit user modal (placeholder)
function showEditUserModal(userId) {
    alert(`Edit user functionality for user ID: ${userId} would be implemented here.`);
    // In a real implementation, you would:
    // 1. Fetch user data
    // 2. Populate a modal form
    // 3. Handle the update via AJAX
}

// Safe chart initialization with fixes
function safeInitCharts() {
    try {
        // Add CSS fixes first
        addChartCSSFixes();
        
        // Fix chart containers first
        fixChartContainers();
        
        // Initialize charts after a small delay to ensure DOM is ready
        setTimeout(initCharts, 100);
        
        // Prevent chart resize issues
        preventChartResize();
        
    } catch (error) {
        console.error('Chart initialization failed:', error);
    }
}

// Add CSS fixes for chart containers
function addChartCSSFixes() {
    if (!document.querySelector('#chart-fixes')) {
        const style = document.createElement('style');
        style.id = 'chart-fixes';
        style.textContent = `
            /* Emergency fixes for chart containers */
            .chart-container,
            .activity-chart-container {
                height: 300px !important;
                min-height: 300px !important;
                max-height: 300px !important;
                overflow: hidden !important;
                position: relative !important;
                resize: none !important;
            }
            
            /* Force all chart canvases to stay within bounds */
            .chart-container canvas,
            .activity-chart-container canvas {
                max-width: 100% !important;
                max-height: 280px !important;
                height: 280px !important;
                display: block !important;
                margin: 0 auto !important;
            }
            
            /* Specific fix for polar area charts */
            canvas[data-chart-type="polarArea"] {
                transform: scale(0.85) !important;
                transform-origin: center !important;
            }
            
            /* Prevent content cards from expanding */
            .content-card,
            .analytics-card {
                overflow: hidden !important;
                min-height: auto !important;
            }
            
            /* Fix for chart legends */
            .chartjs-legend {
                max-height: 80px !important;
                overflow: hidden !important;
                margin-top: 10px !important;
            }
            
            /* Ensure no scrollbars appear */
            .chart-container::-webkit-scrollbar,
            .activity-chart-container::-webkit-scrollbar {
                display: none !important;
            }
            
            /* Badge styles */
            .role-badge {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            .role-badge.farmer { background: rgba(74, 124, 89, 0.1); color: #4a7c59; }
            .role-badge.supporter { background: rgba(141, 181, 150, 0.1); color: #8db596; }
            .role-badge.researcher { background: rgba(212, 175, 55, 0.1); color: #d4af37; }
            .role-badge.admin { background: rgba(255, 154, 61, 0.1); color: #ff9a3d; }
            
            .status-badge {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            .status-badge.active { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
            .status-badge.inactive { background: rgba(156, 163, 175, 0.1); color: #6b7280; }
            .status-badge.suspended { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
            
            .btn-action {
                background: none;
                border: none;
                cursor: pointer;
                padding: 6px;
                border-radius: 4px;
                transition: all 0.3s ease;
                margin: 0 2px;
            }
            .btn-action.edit { color: #4a7c59; }
            .btn-action.delete { color: #ef4444; }
            .btn-action:hover { background: rgba(0,0,0,0.05); transform: scale(1.1); }
            
            /* Farmer card styles */
            .farmer-card {
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 16px;
                background: white;
                transition: all 0.3s ease;
            }
            .farmer-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
            .farmer-card-header {
                display: flex;
                align-items: center;
                margin-bottom: 12px;
            }
            .farmer-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #4a7c59;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
            }
            .farmer-stats {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 12px;
            }
            .farmer-stat {
                text-align: center;
                padding: 8px;
                background: #f8fafc;
                border-radius: 6px;
            }
            .farmer-stat-value {
                display: block;
                font-weight: bold;
                color: #2d3748;
            }
            .farmer-stat-label {
                display: block;
                font-size: 0.8rem;
                color: #718096;
            }
            
            /* Campaign card styles */
            .campaign-card {
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                background: white;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            .campaign-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
            .campaign-header {
                padding: 16px;
                background: #f8fafc;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }
            .campaign-title {
                font-weight: bold;
                color: #2d3748;
            }
            .campaign-farmer {
                font-size: 0.9rem;
                color: #718096;
            }
            .campaign-type {
                background: #4a7c59;
                color: white;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 0.8rem;
            }
            .campaign-body {
                padding: 16px;
            }
            .campaign-progress {
                margin: 12px 0;
            }
            .progress-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 4px;
            }
            .progress-bar {
                height: 6px;
                background: #e2e8f0;
                border-radius: 3px;
                overflow: hidden;
            }
            .progress-fill {
                height: 100%;
                background: #4a7c59;
                transition: width 0.3s ease;
            }
            .campaign-meta {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-top: 12px;
            }
            .campaign-meta-item {
                text-align: center;
                padding: 8px;
                background: #f8fafc;
                border-radius: 6px;
            }
            .campaign-meta-label {
                display: block;
                font-size: 0.8rem;
                color: #718096;
            }
            .campaign-meta-value {
                display: block;
                font-weight: bold;
                color: #2d3748;
            }
            .campaign-footer {
                padding: 12px 16px;
                background: #f8fafc;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .campaign-status {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            .campaign-status.active { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
            .campaign-status.completed { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        `;
        document.head.appendChild(style);
    }
}

// Initialize login chart
function initLoginChart() {
    const loginCtx = document.getElementById('loginChart');
    if (!loginCtx) return;
    
    // This would typically come from an API call
    const loginData = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Daily Logins',
            data: [12, 19, 8, 15, 12, 18, 10],
            backgroundColor: 'rgba(25, 118, 210, 0.2)',
            borderColor: 'rgba(25, 118, 210, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };
    
    new Chart(loginCtx.getContext('2d'), {
        type: 'line',
        data: loginData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });
}

// Fix chart container dimensions
function fixChartContainers() {
    const chartContainers = document.querySelectorAll('.activity-chart-container, .chart-container');
    chartContainers.forEach(container => {
        // Ensure fixed dimensions
        container.style.height = '300px';
        container.style.minHeight = '300px';
        container.style.maxHeight = '300px';
        container.style.overflow = 'hidden';
        container.style.position = 'relative';
        
        // Remove any inline styles that might cause issues
        container.style.flex = 'none';
        container.style.flexGrow = '0';
        container.style.flexShrink = '0';
    });
}

// Prevent chart auto-resizing and scrolling issues
function preventChartResize() {
    if (typeof Chart === 'undefined') return;
    
    // Override Chart.js resize behavior for problematic charts
    const originalResize = Chart.Chart.prototype.resize;
    Chart.Chart.prototype.resize = function() {
        if (!this.canvas) return;
        
        const container = this.canvas.parentElement;
        if (container && container.offsetHeight > 350) {
            container.style.height = '300px';
            container.style.overflow = 'hidden';
        }
        
        // Only resize if within reasonable bounds
        const rect = this.canvas.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0 && rect.height < 500) {
            originalResize.call(this);
        }
    };
    
    // Debounce window resize events for charts
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (window.Chart && Chart.instances) {
                Object.values(Chart.instances).forEach(chart => {
                    if (chart.canvas) {
                        const rect = chart.canvas.getBoundingClientRect();
                        // Only resize if chart is visible and has proper dimensions
                        if (rect.width > 0 && rect.height > 0 && rect.height < 400) {
                            try {
                                chart.resize();
                            } catch (e) {
                                console.warn('Chart resize error:', e);
                            }
                        }
                    }
                });
            }
        }, 500);
    });
}

// Initialize charts with fixed options
function initCharts() {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded');
        return;
    }

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 800,
            easing: 'easeOutQuart'
        },
        layout: {
            padding: {
                top: 10,
                bottom: 10,
                left: 10,
                right: 10
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    boxWidth: 12,
                    padding: 15,
                    usePointStyle: true
                }
            }
        }
    };

    // Activity Chart
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        new Chart(activityCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'User Registrations',
                    data: [120, 190, 300, 500, 200, 300],
                    borderColor: '#4a7c59',
                    backgroundColor: 'rgba(74, 124, 89, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Campaigns Created',
                    data: [70, 120, 180, 240, 160, 220],
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            maxTicksLimit: 6
                        }
                    },
                    x: {
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }
                }
            }
        });
    }
    
    // User Distribution Chart
    const userDistributionCtx = document.getElementById('userDistributionChart');
    if (userDistributionCtx) {
        new Chart(userDistributionCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Farmers', 'Supporters', 'Researchers', 'Administrators'],
                datasets: [{
                    data: [65, 25, 8, 2],
                    backgroundColor: [
                        '#4a7c59',
                        '#8db596',
                        '#d4af37',
                        '#ff9a3d'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                },
                cutout: '50%'
            }
        });
    }
    
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx) {
        new Chart(userGrowthCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Users',
                    data: [320, 450, 380, 520, 610, 730],
                    backgroundColor: '#4a7c59'
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            maxTicksLimit: 6
                        }
                    },
                    x: {
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }
                }
            }
        });
    }
    
    // Regional Distribution Chart
    const regionalCtx = document.getElementById('regionalDistributionChart');
    if (regionalCtx) {
        new Chart(regionalCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Luzon', 'Visayas', 'Mindanao'],
                datasets: [{
                    data: [55, 25, 20],
                    backgroundColor: [
                        '#4a7c59',
                        '#8db596',
                        '#d4af37'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                }
            }
        });
    }
    
    // Campaign Performance Chart
    const campaignCtx = document.getElementById('campaignPerformanceChart');
    if (campaignCtx) {
        new Chart(campaignCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Successful', 'In Progress', 'Failed'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: [
                        '#4a7c59',
                        '#d4af37',
                        '#ff9a3d'
                    ]
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                }
            }
        });
    }
    
    // Yield Improvement Chart
    const yieldCtx = document.getElementById('yieldImprovementChart');
    if (yieldCtx) {
        new Chart(yieldCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['2020', '2021', '2022', '2023', '2024'],
                datasets: [{
                    label: 'Average Yield (tons/ha)',
                    data: [3.2, 3.5, 3.8, 4.2, 4.8],
                    borderColor: '#4a7c59',
                    backgroundColor: 'rgba(74, 124, 89, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 3,
                        ticks: {
                            maxTicksLimit: 6
                        }
                    },
                    x: {
                        ticks: {
                            maxTicksLimit: 5
                        }
                    }
                }
            }
        });
    }
}

// Load sample data
function loadSampleData() {
    // Load users table data
    loadUsersData();
    
    // Load farmers data
    loadFarmersData();
    
    // Load campaigns data
    loadCampaignsData();
}

// Load users data - UPDATED VERSION
function loadUsersData() {
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;

    // Show loading state
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">Loading users...</td></tr>';

    // Fetch real data from PHP
    fetch('get_users.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(users => {
            displayUsers(users);
        })
        .catch(error => {
            console.error('Error loading users:', error);
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #ef4444;">Error loading users. Please refresh the page.</td></tr>';
        });
}

// Display users in the table
function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px;">No users found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';

    users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${user.id}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td><span class="role-badge role-${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span></td>
            <td><span class="status-badge status-${user.status}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span></td>
            <td>${formatDate(user.created_at)}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon edit-user" data-id="${user.id}" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-icon delete-user" data-id="${user.id}" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    // Re-attach event listeners to the new delete buttons
    attachDeleteHandlers();
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

// Attach delete handlers to delete buttons
function attachDeleteHandlers() {
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                deleteUser(userId);
            }
        });
    });
}

// Delete user function
function deleteUser(userId) {
    fetch(`delete_user.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User deleted successfully!', 'success');
                // Reload users data
                loadUsersData();
            } else {
                showNotification(data.error || 'Failed to delete user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting user. Please try again.', 'error');
        });
}

// Load campaigns data
function loadCampaignsData() {
    const campaigns = [
        {
            title: 'Modern Irrigation System',
            farmer: 'Juan Dela Cruz',
            type: 'Infrastructure',
            goal: 75000,
            raised: 68250,
            deadline: '2024-08-15',
            supporters: 124,
            status: 'Active'
        },
        {
            title: 'Organic Fertilizer Program',
            farmer: 'Maria Santos',
            type: 'Inputs',
            goal: 25000,
            raised: 25000,
            deadline: '2024-07-30',
            supporters: 89,
            status: 'Completed'
        },
        {
            title: 'Rice Transplanting Machine',
            farmer: 'Pedro Reyes',
            type: 'Equipment',
            goal: 120000,
            raised: 85400,
            deadline: '2024-09-20',
            supporters: 203,
            status: 'Active'
        },
        {
            title: 'Hybrid Seeds Distribution',
            farmer: 'Luis Tan',
            type: 'Seeds',
            goal: 45000,
            raised: 32150,
            deadline: '2024-10-15',
            supporters: 67,
            status: 'Active'
        }
    ];
    
    const grid = document.getElementById('campaignsGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    campaigns.forEach(campaign => {
        const progress = Math.round((campaign.raised / campaign.goal) * 100);
        const daysLeft = Math.max(0, Math.ceil((new Date(campaign.deadline) - new Date()) / (1000 * 60 * 60 * 24)));
        
        const card = document.createElement('div');
        card.className = 'campaign-card';
        card.innerHTML = `
            <div class="campaign-header">
                <div>
                    <div class="campaign-title">${campaign.title}</div>
                    <div class="campaign-farmer">by ${campaign.farmer}</div>
                </div>
                <span class="campaign-type">${campaign.type}</span>
            </div>
            <div class="campaign-body">
                <div class="campaign-description">
                    This campaign aims to improve rice farming efficiency through modern equipment and techniques.
                </div>
                <div class="campaign-progress">
                    <div class="progress-info">
                        <span class="progress-raised">₱${campaign.raised.toLocaleString()} raised</span>
                        <span class="progress-percentage">${progress}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${progress}%"></div>
                    </div>
                </div>
                <div class="campaign-meta">
                    <div class="campaign-meta-item">
                        <span class="campaign-meta-label">Goal</span>
                        <span class="campaign-meta-value">₱${campaign.goal.toLocaleString()}</span>
                    </div>
                    <div class="campaign-meta-item">
                        <span class="campaign-meta-label">Supporters</span>
                        <span class="campaign-meta-value">${campaign.supporters}</span>
                    </div>
                    <div class="campaign-meta-item">
                        <span class="campaign-meta-label">Deadline</span>
                        <span class="campaign-meta-value">${campaign.deadline}</span>
                    </div>
                    <div class="campaign-meta-item">
                        <span class="campaign-meta-label">Days Left</span>
                        <span class="campaign-meta-value">${daysLeft}</span>
                    </div>
                </div>
            </div>
            <div class="campaign-footer">
                <span class="campaign-status ${campaign.status.toLowerCase()}">${campaign.status}</span>
                <div>
                    <button class="btn btn-sm btn-primary">View Details</button>
                    <button class="btn btn-sm btn-outline">Edit</button>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
}

// Load analytics data based on timeframe
function loadAnalyticsData(timeframe) {
    console.log('Loading analytics data for timeframe:', timeframe);
    // In a real implementation, this would fetch data from the server
    // based on the selected timeframe and update the charts
}

// Load page-specific data
function loadPageData(pageId) {
    switch(pageId) {
        case 'userManagement':
            console.log('Loading user management data...');
            break;
        case 'farmers':
            console.log('Loading farmer management data...');
            break;
        case 'campaigns':
            console.log('Loading campaign management data...');
            break;
        case 'analytics':
            console.log('Loading analytics data...');
            break;
        case 'system':
            console.log('Loading system settings...');
            break;
    }
}

// Add new user - UPDATED VERSION
function addNewUser() {
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding User...';
    submitBtn.disabled = true;

    // Send data to PHP using Fetch API
    fetch('add_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('User added successfully!', 'success');
            document.getElementById('addUserModal').style.display = 'none';
            form.reset();
            
            // ✅ RELOAD USERS DATA AUTOMATICALLY
            loadUsersData();
            
            // ✅ ALSO UPDATE THE DASHBOARD STATS
            updateDashboardStats();
        } else {
            showNotification(data.error || 'Failed to add user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding user. Please try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Function to update dashboard stats
function updateDashboardStats() {
    // Update the total users count on the dashboard
    const totalUsersElement = document.getElementById('totalUsers');
    if (totalUsersElement) {
        // Increment the count by 1 (or you could fetch fresh data)
        const currentCount = parseInt(totalUsersElement.textContent) || 0;
        totalUsersElement.textContent = currentCount + 1;
    }
}

// Add new farmer
function addNewFarmer() {
    const form = document.getElementById('addFarmerForm');
    
    // In a real app, this would be an API call
    console.log('Adding new farmer:', {
        name: document.getElementById('farmerName').value,
        experience: document.getElementById('farmerExperience').value,
        location: document.getElementById('farmerLocation').value,
        farmSize: document.getElementById('farmSize').value
    });
    
    showNotification('Farmer registered successfully!', 'success');
    document.getElementById('addFarmerModal').style.display = 'none';
    form.reset();
    
    // Refresh farmers grid
    loadFarmersData();
}

// Create new campaign
function createNewCampaign() {
    const form = document.getElementById('createCampaignForm');
    
    // In a real app, this would be an API call
    console.log('Creating new campaign:', {
        title: document.getElementById('campaignTitle').value,
        type: document.getElementById('campaignType').value,
        goal: document.getElementById('campaignGoal').value,
        description: document.getElementById('campaignDescription').value,
        deadline: document.getElementById('campaignDeadline').value
    });
    
    showNotification('Campaign created successfully!', 'success');
    document.getElementById('createCampaignModal').style.display = 'none';
    form.reset();
    
    // Refresh campaigns grid
    loadCampaignsData();
}

// Emergency fix function - call this if charts still cause issues
function emergencyChartFix() {
    // Force all chart containers to fixed dimensions
    const allContainers = document.querySelectorAll('.chart-container, .activity-chart-container, .content-card, .analytics-card');
    allContainers.forEach(container => {
        container.style.height = '300px';
        container.style.maxHeight = '300px';
        container.style.overflow = 'hidden';
        container.style.flex = 'none';
    });
    
    // Force all canvases to fixed dimensions
    const allCanvases = document.querySelectorAll('canvas');
    allCanvases.forEach(canvas => {
        canvas.style.maxHeight = '280px';
        canvas.style.height = '280px';
    });
}

// Export data functionality
function initExportButtons() {
    document.querySelectorAll('.btn-outline').forEach(button => {
        if (button.textContent.includes('Export')) {
            button.addEventListener('click', function() {
                const page = document.querySelector('.page.active').id;
                let dataType = 'data';
                
                if (page.includes('user')) dataType = 'users';
                else if (page.includes('farmer')) dataType = 'farmers';
                else if (page.includes('campaign')) dataType = 'campaigns';
                
                showNotification(`${dataType.charAt(0).toUpperCase() + dataType.slice(1)} data exported successfully!`, 'success');
            });
        }
    });
}

// Initialize export buttons
initExportButtons();

// Make functions available globally for debugging
window.adminDashboard = {
    reloadUsers: loadUsersData,
    reloadFarmers: loadFarmersData,
    reloadCampaigns: loadCampaignsData,
    emergencyFix: emergencyChartFix,
    showNotification: showNotification
};