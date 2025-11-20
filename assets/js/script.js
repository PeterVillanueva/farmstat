// Data Storage and State Management
let appState = {
    currentUser: null,
    farmers: [],
    campaigns: [],
    activities: [],
    currentPage: 'overview',
    isAuthenticated: false,
    seasonalData: {},
    monitoringData: {},
    analyticsData: {}
};

// Chart instances storage
const chartInstances = {};

// Initialize the application
function initApp() {
    console.log('Initializing FarmStat application...');
    checkAuthStatus();
    setupEventListeners();
    
    // Load initial data
    loadInitialData();
}

// Check authentication status from session
function checkAuthStatus() {
    console.log('Checking authentication status...');
    fetch('/farmstat/api/auth/check')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Auth check result:', data);
            if (data.authenticated) {
                appState.currentUser = {
                    id: data.user_id,
                    name: data.user_name,
                    email: data.user_email,
                    role: data.user_role
                };
                appState.isAuthenticated = true;
                updateUserWelcome();
                
                // If user is authenticated, show dashboard immediately
                if (!document.getElementById('dashboard').classList.contains('hidden')) {
                    showDashboard();
                }
            } else {
                console.log('User not authenticated, showing landing page');
                showLandingPage();
            }
        })
        .catch(error => {
            console.error('Error checking auth status:', error);
            showLandingPage();
        });
}
// Setup event listeners
function setupEventListeners() {
    // Auth Modal
    document.getElementById('showLogin')?.addEventListener('click', () => showAuthModal('login'));
    document.getElementById('showSignup')?.addEventListener('click', () => showAuthModal('signup'));
    
    // Auth Tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', (e) => {
            const tabName = e.target.getAttribute('data-tab');
            switchAuthTab(tabName);
        });
    });
    
    // Auth Forms
    document.getElementById('loginForm')?.addEventListener('submit', handleLogin);
    document.getElementById('signupForm')?.addEventListener('submit', handleSignup);
    
    // Navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', (e) => {
            const page = e.currentTarget.getAttribute('data-page');
            switchPage(page);
        });
    });
    
    // Logout
    document.getElementById('logoutBtn')?.addEventListener('click', handleLogout);
    
    // Farmer Management
    document.getElementById('addFarmerBtn')?.addEventListener('click', () => showModal('addFarmerModal'));
    document.getElementById('addFarmerForm')?.addEventListener('submit', handleAddFarmer);
    
    // Campaign Management
    document.getElementById('createCampaignBtn')?.addEventListener('click', () => showModal('createCampaignModal'));
    document.getElementById('createCampaignForm')?.addEventListener('submit', handleCreateCampaign);
    
    // Modal Close Events
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal');
            hideModal(modal.id);
        });
    });
    
    // Modal Cancel Buttons
    document.getElementById('cancelAddFarmer')?.addEventListener('click', () => hideModal('addFarmerModal'));
    document.getElementById('cancelCampaign')?.addEventListener('click', () => hideModal('createCampaignModal'));
    
    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideModal(modal.id);
            }
        });
    });
    
    // Landing Page CTA Buttons
    document.getElementById('heroSignup')?.addEventListener('click', () => showAuthModal('signup'));
    document.getElementById('heroDemo')?.addEventListener('click', showDemo);
    document.getElementById('ctaFarmer')?.addEventListener('click', () => showAuthModal('signup'));
    document.getElementById('ctaSupporter')?.addEventListener('click', () => showAuthModal('signup'));
    
    // Profile Page Tabs
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.addEventListener('click', (e) => {
            const tabName = e.target.getAttribute('data-tab');
            switchProfileTab(tabName);
        });
    });
    
    // Profile Form
    document.getElementById('profileForm')?.addEventListener('submit', handleProfileUpdate);
    
    // Avatar Upload
    document.getElementById('changeAvatarBtn')?.addEventListener('click', () => {
        document.getElementById('avatarInput').click();
    });
    
    document.getElementById('avatarInput')?.addEventListener('change', handleAvatarUpload);
}

// Profile Tab Switching
function switchProfileTab(tabName) {
    // Update active tab
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Update active content
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('active');
    });
    document.getElementById(`${tabName}Tab`).classList.add('active');
}

// Profile Handlers
function handleProfileUpdate(e) {
    e.preventDefault();
    
    if (appState.currentUser) {
        const formData = new FormData();
        formData.append('name', document.getElementById('profileFullName').value);
        formData.append('email', document.getElementById('profileEmailInput').value);
        formData.append('phone', document.getElementById('profilePhone').value);
        formData.append('location', document.getElementById('profileLocation').value);
        formData.append('bio', document.getElementById('profileBio').value);
        formData.append('user_id', appState.currentUser.id);
        
        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appState.currentUser.name = data.user.name;
                appState.currentUser.email = data.user.email;
                updateUserWelcome();
                showNotification('Profile updated successfully!', 'success');
            } else {
                showNotification(data.error || 'Failed to update profile', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating profile', 'error');
        });
    }
}

function handleAvatarUpload(e) {
    const file = e.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('user_id', appState.currentUser.id);
        
        fetch('upload_avatar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const avatarPreview = document.getElementById('avatarPreview');
                avatarPreview.innerHTML = `<img src="${data.avatar_url}" alt="Profile Avatar">`;
                showNotification('Profile picture updated!', 'success');
            } else {
                showNotification(data.error || 'Failed to upload avatar', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error uploading avatar', 'error');
        });
    }
}

// Enhanced Chart Initialization with Error Handling
function initCharts() {
    // Only initialize charts for visible elements
    const chartInitializers = {
        varietyChart: createVarietyChart,
        pestChart: createPestChart,
        seasonComparisonChart: createSeasonComparisonChart,
        yieldTrendChart: createYieldTrendChart,
        costAnalysisChart: createCostAnalysisChart,
        regionalYieldChart: createRegionalYieldChart,
        technologyAdoptionChart: createTechnologyAdoptionChart,
        rainfallImpactChart: createRainfallImpactChart
    };

    Object.entries(chartInitializers).forEach(([chartId, initializer]) => {
        const canvas = document.getElementById(chartId);
        if (canvas && canvas.getContext) {
            try {
                // Destroy existing chart if it exists
                if (chartInstances[chartId]) {
                    chartInstances[chartId].destroy();
                }
                chartInstances[chartId] = initializer();
            } catch (error) {
                console.warn(`Failed to initialize chart ${chartId}:`, error);
            }
        }
    });
}

// Page-specific chart initializers
function initOverviewCharts() {
    createVarietyChart();
    createRegionalYieldChart();
}

function initSeasonalCharts() {
    createSeasonComparisonChart();
    createYieldTrendChart();
    createTechnologyAdoptionChart();
    createRainfallImpactChart();
}

function initMonitoringCharts() {
    createPestChart();
}

function initAnalyticsCharts() {
    createCostAnalysisChart();
    createRegionalYieldChart();
    createTechnologyAdoptionChart();
    createRainfallImpactChart();
}

// Individual Chart Creation Functions with Real Data
function createVarietyChart() {
    const ctx = document.getElementById('varietyChart');
    if (!ctx || !ctx.getContext) return null;
    
    // Fetch real data from database
    fetch('get_variety_data.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            '#4a7c59',
                            '#8db596',
                            '#d4af37',
                            '#ff9a3d',
                            '#1a4d2e'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        title: {
                            display: true,
                            text: 'Rice Variety Distribution',
                            font: { size: 16 }
                        }
                    },
                    cutout: '60%'
                }
            });
        })
        .catch(error => {
            console.error('Error loading variety data:', error);
        });
    
    return null;
}

function createPestChart() {
    const ctx = document.getElementById('pestChart');
    if (!ctx || !ctx.getContext) return null;
    
    // Fetch pest data from database
    fetch('get_pest_data.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Incidence Rate (%)',
                        data: data.values,
                        backgroundColor: [
                            '#ff9a3d',
                            '#d4af37',
                            '#4a7c59',
                            '#8db596'
                        ],
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 20,
                            title: {
                                display: true,
                                text: 'Incidence Rate (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Pest Type'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Current Pest Incidence',
                            font: { size: 16 }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading pest data:', error);
        });
    
    return null;
}

function createSeasonComparisonChart() {
    const ctx = document.getElementById('seasonComparisonChart');
    if (!ctx || !ctx.getContext) return null;
    
    // Fetch seasonal data from database
    fetch('get_seasonal_data.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.years,
                    datasets: [
                        {
                            label: 'Yield (tons/ha)',
                            data: data.yields,
                            borderColor: '#4a7c59',
                            backgroundColor: 'rgba(74, 124, 89, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Production Cost (₱ thousands)',
                            data: data.costs,
                            borderColor: '#d4af37',
                            backgroundColor: 'rgba(212, 175, 55, 0.1)',
                            tension: 0.4,
                            fill: false,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Yield (tons/ha)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Cost (₱ thousands)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Yield vs Cost Over 5 Years',
                            font: { size: 16 }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading seasonal data:', error);
        });
    
    return null;
}

function createYieldTrendChart() {
    const ctx = document.getElementById('yieldTrendChart');
    if (!ctx || !ctx.getContext) return null;
    
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['2020 Dry', '2021 Dry', '2022 Dry', '2023 Dry', '2024 Dry'],
            datasets: [{
                label: 'Yield (tons/ha)',
                data: [3.2, 3.5, 4.1, 4.8, 5.2],
                backgroundColor: ['#8db596', '#8db596', '#8db596', '#8db596', '#4a7c59'],
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Yield (tons/ha)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Seasonal Yield Performance',
                    font: { size: 16 }
                }
            }
        }
    });
}

function createCostAnalysisChart() {
    const ctx = document.getElementById('costAnalysisChart');
    if (!ctx || !ctx.getContext) return null;
    
    return new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Seeds', 'Fertilizers', 'Labor', 'Equipment', 'Others'],
            datasets: [{
                data: [4500, 8500, 7500, 4500, 3500],
                backgroundColor: [
                    '#4a7c59',
                    '#8db596',
                    '#d4af37',
                    '#ff9a3d',
                    '#1a4d2e'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                },
                title: {
                    display: true,
                    text: 'Production Cost Breakdown',
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const percentages = [16, 30, 26, 16, 12];
                            const label = context.label;
                            const value = context.raw;
                            const percentage = percentages[context.dataIndex];
                            return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function createRegionalYieldChart() {
    const ctx = document.getElementById('regionalYieldChart');
    if (!ctx || !ctx.getContext) return null;
    
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Nueva Ecija', 'Isabela', 'Tarlac', 'Pangasinan', 'Bulacan', 'Pampanga'],
            datasets: [{
                label: 'Average Yield (tons/ha)',
                data: [4.8, 4.2, 5.1, 3.9, 4.1, 4.3],
                backgroundColor: ['#4a7c59', '#8db596', '#4a7c59', '#8db596', '#4a7c59', '#8db596'],
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Yield (tons/ha)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Regional Yield Comparison',
                    font: { size: 16 }
                }
            }
        }
    });
}

function createTechnologyAdoptionChart() {
    const ctx = document.getElementById('technologyAdoptionChart');
    if (!ctx || !ctx.getContext) return null;
    
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: [2020, 2021, 2022, 2023, 2024],
            datasets: [{
                label: 'Technology Adoption Rate (%)',
                data: [15, 22, 45, 65, 78],
                borderColor: '#ff9a3d',
                backgroundColor: 'rgba(255, 154, 61, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Adoption Rate (%)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Technology Adoption Timeline',
                    font: { size: 16 }
                }
            }
        }
    });
}

function createRainfallImpactChart() {
    const ctx = document.getElementById('rainfallImpactChart');
    if (!ctx || !ctx.getContext) return null;
    
    return new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Rainfall vs Yield',
                data: [
                    { x: 1100, y: 3.2 },
                    { x: 1050, y: 3.5 },
                    { x: 1200, y: 4.1 },
                    { x: 1180, y: 4.8 },
                    { x: 1250, y: 5.2 }
                ],
                backgroundColor: '#4a7c59',
                pointRadius: 8,
                pointHoverRadius: 10
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Rainfall (mm)'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Yield (tons/ha)'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Rainfall Impact on Yield',
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const years = [2020, 2021, 2022, 2023, 2024];
                            const point = context.raw;
                            const year = years[context.dataIndex];
                            return `${year}: ${point.y} tons/ha at ${point.x}mm rainfall`;
                        }
                    }
                }
            }
        }
    });
}

// Enhanced Page Rendering Functions with Database Integration
function renderSeasonalTracking() {
    const container = document.getElementById('seasonalTrackingPage');
    if (!container) return;
    
    // Fetch real seasonal data
    fetch('get_seasonal_stats.php')
        .then(response => response.json())
        .then(data => {
            const metricsContainer = container.querySelector('.metrics-grid');
            if (metricsContainer) {
                metricsContainer.innerHTML = `
                    <div class="metric-card">
                        <div class="metric-header">
                            <span class="metric-title">Average Yield</span>
                            <span class="metric-trend positive">+${data.yield_increase}%</span>
                        </div>
                        <div class="metric-value">${data.average_yield} tons/ha</div>
                        <div class="metric-period">5-Year Average</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-header">
                            <span class="metric-title">Production Cost</span>
                            <span class="metric-trend negative">+${data.cost_increase}%</span>
                        </div>
                        <div class="metric-value">₱${data.current_cost.toLocaleString()}/ha</div>
                        <div class="metric-period">Current Season</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-header">
                            <span class="metric-title">Profit Margin</span>
                            <span class="metric-trend positive">+${data.profit_increase}%</span>
                        </div>
                        <div class="metric-value">₱${data.current_profit.toLocaleString()}/ha</div>
                        <div class="metric-period">5-Year Improvement</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-header">
                            <span class="metric-title">Technology Adoption</span>
                            <span class="metric-trend positive">+${data.tech_increase}%</span>
                        </div>
                        <div class="metric-value">${data.tech_adoption}%</div>
                        <div class="metric-period">Since 2020</div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading seasonal stats:', error);
        });
}

function renderRiceMonitoring() {
    const container = document.getElementById('riceMonitoringPage');
    if (!container) return;
    
    // Fetch real monitoring data
    fetch('get_monitoring_data.php')
        .then(response => response.json())
        .then(data => {
            // Update fertilizer progress
            const inputGrid = container.querySelector('.input-grid');
            if (inputGrid) {
                inputGrid.innerHTML = Object.entries(data.fertilizer_progress).map(([key, fert]) => `
                    <div class="input-item">
                        <span class="input-name">${key.charAt(0).toUpperCase() + key.slice(1)}</span>
                        <span class="input-usage">${fert.applied}% applied</span>
                        <div class="progress-bar small">
                            <div class="progress-fill" style="width: ${fert.applied}%"></div>
                        </div>
                        <span class="input-details">${fert.total} ${key === 'organic' ? 'kg/ha' : 'units'}, ${fert.method}</span>
                    </div>
                `).join('');
            }
            
            // Update pest alerts
            const alertList = container.querySelector('.alert-list');
            if (alertList) {
                alertList.innerHTML = data.pest_alerts.map(alert => `
                    <div class="alert-item ${alert.status}">
                        <i class="fas fa-${getAlertIcon(alert.status)}"></i>
                        <span>${alert.pest} - ${alert.severity.charAt(0).toUpperCase() + alert.severity.slice(1)} Incidence (${alert.incidence}% fields)</span>
                    </div>
                `).join('');
            }
            
            // Update yield prediction
            const predictionOverview = container.querySelector('.prediction-overview');
            if (predictionOverview) {
                predictionOverview.innerHTML = `
                    <div class="prediction-card">
                        <h4>Expected Yield</h4>
                        <div class="prediction-value">${data.yield_prediction.expected} tons/ha</div>
                        <div class="prediction-confidence">${data.yield_prediction.confidence}% Confidence</div>
                    </div>
                    <div class="prediction-card">
                        <h4>Compared to Last Season</h4>
                        <div class="prediction-value positive">+${data.yield_prediction.comparison}%</div>
                        <div class="prediction-note">Improved practices</div>
                    </div>
                    <div class="prediction-card">
                        <h4>Harvest Readiness</h4>
                        <div class="prediction-value">${data.yield_prediction.harvest_readiness} days</div>
                        <div class="prediction-note">Until optimal harvest</div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading monitoring data:', error);
        });
}

function renderAnalytics() {
    // Charts are initialized separately
}

// Utility Functions
function getAlertIcon(status) {
    const icons = {
        warning: 'exclamation-triangle',
        info: 'info-circle',
        safe: 'check-circle'
    };
    return icons[status] || 'info-circle';
}

// Auth Modal Functions
function showAuthModal(tab = 'login') {
    document.getElementById('authModal').style.display = 'flex';
    switchAuthTab(tab);
}

function hideAuthModal() {
    document.getElementById('authModal').style.display = 'none';
}

function switchAuthTab(tab) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    document.getElementById(`${tab}Form`).classList.add('active');
}

// Auth Handlers with Database Integration
function handleLogin(e) {
    e.preventDefault();
    console.log('Login attempt...');
    
    const formData = new FormData();
    formData.append('email', document.getElementById('loginEmail').value);
    formData.append('password', document.getElementById('loginPassword').value);
    
    fetch('/farmstat/login', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Login response:', data);
        if (data.success) {
            appState.currentUser = {
                id: data.user_id,
                name: data.user_name,
                email: data.user_email,
                role: data.user_role
            };
            appState.isAuthenticated = true;
            
            showNotification('Login successful!', 'success');
            hideAuthModal();
            showDashboard();
        } else {
            showNotification(data.error || 'Login failed', 'error');
        }
    })
    .catch(error => {
        console.error('Login error:', error);
        showNotification('Login error. Please try again.', 'error');
    });
}

function handleSignup(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('name', document.getElementById('signupName').value);
    formData.append('email', document.getElementById('signupEmail').value);
    formData.append('password', document.getElementById('signupPassword').value);
    formData.append('confirm_password', document.getElementById('signupConfirm').value);
    formData.append('role', document.getElementById('signupRole').value);
    
    fetch('/farmstat/register', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Account created successfully! Please login.', 'success');
            hideAuthModal();
            switchAuthTab('login');
        } else {
            showNotification(data.error || 'Registration failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Registration error. Please try again.', 'error');
    });
}

function handleLogout() {
    fetch('/farmstat/logout')
        .then(response => {
            // Logout redirects, so just handle the redirect
            window.location.href = '/farmstat/login';
        })
        .then(data => {
            appState.currentUser = null;
            appState.isAuthenticated = false;
            showLandingPage();
            showNotification('Logged out successfully', 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Logout error', 'error');
        });
}

// Update user welcome message
function updateUserWelcome() {
    if (appState.currentUser) {
        const userWelcome = document.getElementById('userWelcome');
        const profileName = document.getElementById('profileName');
        const profileFullName = document.getElementById('profileFullName');
        const profileEmailInput = document.getElementById('profileEmailInput');
        
        if (userWelcome) {
            userWelcome.textContent = `Welcome, ${appState.currentUser.name}`;
        }
        if (profileName) {
            profileName.textContent = appState.currentUser.name;
        }
        if (profileFullName) {
            profileFullName.value = appState.currentUser.name;
        }
        if (profileEmailInput) {
            profileEmailInput.value = appState.currentUser.email;
        }
        
        console.log('Updated user welcome for:', appState.currentUser.name);
    }
}


function loadInitialData() {
    if (appState.isAuthenticated) {
        loadDashboardData();
        loadFarmersData();
        loadCampaignsData();
    }
}

// Page Navigation
function switchPage(page) {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-page') === page) {
            item.classList.add('active');
        }
    });
    
    document.querySelectorAll('.page').forEach(pageElement => {
        pageElement.classList.remove('active');
    });
    
    if (document.getElementById(`${page}Page`)) {
        document.getElementById(`${page}Page`).classList.add('active');
    }
    
    const pageTitles = {
        overview: 'Rice Farming Intelligence Dashboard',
        farmers: 'Rice Farmer Management',
        riceMonitoring: 'Rice Farming Monitoring',
        seasonalTracking: 'Multi-Season Analytics',
        livestock: 'Livestock Integration Management',
        crowdfunding: 'Community Support & Funding',
        analytics: 'Advanced Analytics & Insights',
        community: 'Farmer Community & Knowledge Sharing',
        profile: 'Farmer Profile & Settings'
    };
    
    document.getElementById('pageTitle').textContent = pageTitles[page] || 'FarmStat Dashboard';
    appState.currentPage = page;
    
    // Load page-specific data and charts with delay to ensure DOM is ready
    setTimeout(() => {
        switch (page) {
            case 'overview':
                loadDashboardData();
                initOverviewCharts();
                break;
            case 'farmers':
                loadFarmersData();
                break;
            case 'crowdfunding':
                loadCampaignsData();
                break;
            case 'community':
                loadCommunityData();
                break;
            case 'profile':
                loadProfileData();
                break;
            case 'seasonalTracking':
                renderSeasonalTracking();
                initSeasonalCharts();
                break;
            case 'riceMonitoring':
                renderRiceMonitoring();
                initMonitoringCharts();
                break;
            case 'analytics':
                loadAnalyticsData();
                initAnalyticsCharts();
                break;
        }
    }, 100);
}

// Dashboard Functions
function showDashboard() {
    console.log('Showing dashboard for user:', appState.currentUser);
    document.getElementById('landingPage').classList.add('hidden');
    document.getElementById('dashboard').classList.remove('hidden');
    
    updateUserWelcome();
    loadDashboardData();
    
    // Initialize charts with a delay to ensure DOM is ready
    setTimeout(() => {
        initCharts();
    }, 500);
}

function showLandingPage() {
    document.getElementById('landingPage').classList.remove('hidden');
    document.getElementById('dashboard').classList.add('hidden');
}

function showDemo() {
    // Demo mode with sample data
    appState.currentUser = {
        name: 'Demo Farmer',
        email: 'demo@farmstat.com',
        role: 'farmer'
    };
    appState.isAuthenticated = true;
    showDashboard();
    showNotification('Demo mode activated!', 'success');
}

// Data Loading Functions with Database Integration
function loadDashboardData() {
    fetch('/farmstat/api/dashboard')
        .then(response => response.json())
        .then(data => {
            updateDashboardStats(data);
            renderActivities(data.activities);
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
        });
}

function loadFarmersData() {
    console.log('Loading farmers data...');
    fetch('/farmstat/api/farmers')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Farmers data loaded:', data);
            appState.farmers = data;
            renderFarmers();
        })
        .catch(error => {
            console.error('Error loading farmers data:', error);
            showNotification('Error loading farmers data', 'error');
        });
}

function loadCampaignsData() {
    fetch('/farmstat/api/campaigns')
        .then(response => response.json())
        .then(data => {
            appState.campaigns = data;
            renderCampaigns();
        })
        .catch(error => {
            console.error('Error loading campaigns data:', error);
        });
}

function loadCommunityData() {
    fetch('get_community_data.php')
        .then(response => response.json())
        .then(data => {
            renderLeaderboard(data.leaderboard);
            renderKnowledge(data.knowledge);
        })
        .catch(error => {
            console.error('Error loading community data:', error);
        });
}

function loadAnalyticsData() {
    // Analytics data is loaded by individual charts
}

function loadProfileData() {
    if (appState.currentUser) {
        fetch(`get_profile.php?user_id=${appState.currentUser.id}`)
            .then(response => response.json())
            .then(data => {
                const profileName = document.getElementById('profileName');
                const profileRole = document.getElementById('profileRole');
                const profileFullName = document.getElementById('profileFullName');
                const profileEmailInput = document.getElementById('profileEmailInput');
                const profilePhone = document.getElementById('profilePhone');
                const profileLocation = document.getElementById('profileLocation');
                const profileBio = document.getElementById('profileBio');
                
                if (profileName) profileName.textContent = data.name;
                if (profileRole) profileRole.textContent = `${data.role.charAt(0).toUpperCase() + data.role.slice(1)} - ${data.experience || '15'} years experience`;
                if (profileFullName) profileFullName.value = data.name;
                if (profileEmailInput) profileEmailInput.value = data.email;
                if (profilePhone) profilePhone.value = data.phone || '';
                if (profileLocation) profileLocation.value = data.location || '';
                if (profileBio) profileBio.value = data.bio || '';
                
                // Update avatar if exists
                if (data.avatar) {
                    const avatarPreview = document.getElementById('avatarPreview');
                    avatarPreview.innerHTML = `<img src="${data.avatar}" alt="Profile Avatar">`;
                }
            })
            .catch(error => {
                console.error('Error loading profile data:', error);
            });
    }
}

// Stats and Data Rendering
function updateDashboardStats(data) {
    if (document.getElementById('totalFarmers')) {
        document.getElementById('totalFarmers').textContent = data.total_farmers || 0;
    }
    if (document.getElementById('activeCrops')) {
        document.getElementById('activeCrops').textContent = data.active_crops || 0;
    }
    if (document.getElementById('totalFunding')) {
        document.getElementById('totalFunding').textContent = `₱${((data.total_funding || 0) / 1000).toFixed(1)}k`;
    }
    if (document.getElementById('yieldIncrease')) {
        document.getElementById('yieldIncrease').textContent = data.yield_increase || '156%';
    }
}

function renderFarmers() {
    const farmersGrid = document.getElementById('farmersGrid');
    if (!farmersGrid) return;
    
    farmersGrid.innerHTML = appState.farmers.map(farmer => `
        <div class="farmer-card" data-farmer-id="${farmer.id}">
            <div class="farmer-card-header">
                <div class="farmer-avatar">
                    ${farmer.avatar || (farmer.full_name ? farmer.full_name.split(' ').map(n => n[0]).join('').toUpperCase() : 'FF')}
                </div>
                <div class="farmer-basic-info">
                    <h3>${farmer.full_name || 'Unknown Farmer'}</h3>
                    <p>${farmer.location || 'Unknown Location'} • ${farmer.experience_years || 0} years experience</p>
                </div>
            </div>
            <div class="farmer-card-body">
                <div class="farmer-stats">
                    <div class="farmer-stat">
                        <span class="farmer-stat-value">${farmer.farm_size || 0} ha</span>
                        <span class="farmer-stat-label">Farm Size</span>
                    </div>
                    <div class="farmer-stat">
                        <span class="farmer-stat-value">${farmer.current_yield || 'N/A'}</span>
                        <span class="farmer-stat-label">Yield/ha</span>
                    </div>
                </div>
                <div class="farmer-details">
                    <div class="farmer-detail">
                        <label>Rice Varieties:</label>
                        <span>${farmer.rice_varieties || 'Not specified'}</span>
                    </div>
                    <div class="farmer-detail">
                        <label>Farming Method:</label>
                        <span>${farmer.farming_method || 'Not specified'}</span>
                    </div>
                    <div class="farmer-detail">
                        <label>Land Ownership:</label>
                        <span>${farmer.land_ownership || 'Not specified'}</span>
                    </div>
                </div>
            </div>
            <div class="farmer-card-footer">
                <button class="btn btn-primary btn-sm" onclick="viewFarmerDetails(${farmer.id})">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button class="btn btn-outline btn-sm" onclick="supportFarmer(${farmer.id})">
                    <i class="fas fa-hand-holding-heart"></i> Support
                </button>
            </div>
        </div>
    `).join('');
}

function renderCampaigns() {
    const campaignsGrid = document.getElementById('campaignsGrid');
    if (!campaignsGrid) return;
    
    campaignsGrid.innerHTML = appState.campaigns.map(campaign => {
        const progress = (campaign.current_amount / campaign.goal_amount) * 100;
        return `
            <div class="campaign-card" data-campaign-id="${campaign.id}">
                <div class="campaign-header">
                    <div>
                        <div class="campaign-title">${campaign.title}</div>
                        <div class="campaign-farmer">by ${campaign.farmer_name || 'Community'}</div>
                    </div>
                    <span class="campaign-type">${campaign.type}</span>
                </div>
                <div class="campaign-body">
                    <p class="campaign-description">${campaign.description}</p>
                    
                    <div class="campaign-progress">
                        <div class="progress-info">
                            <span class="progress-raised">₱${campaign.current_amount.toLocaleString()} raised</span>
                            <span class="progress-percentage">${Math.round(progress)}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${progress}%"></div>
                        </div>
                    </div>
                    
                    <div class="campaign-meta">
                        <div class="campaign-meta-item">
                            <span class="campaign-meta-label">Goal</span>
                            <span class="campaign-meta-value">₱${campaign.goal_amount.toLocaleString()}</span>
                        </div>
                        <div class="campaign-meta-item">
                            <span class="campaign-meta-label">Supporters</span>
                            <span class="campaign-meta-value">${campaign.supporters || 0}</span>
                        </div>
                        <div class="campaign-meta-item">
                            <span class="campaign-meta-label">Deadline</span>
                            <span class="campaign-meta-value">${new Date(campaign.deadline).toLocaleDateString()}</span>
                        </div>
                        <div class="campaign-meta-item">
                            <span class="campaign-meta-label">Days Left</span>
                            <span class="campaign-meta-value">${Math.ceil((new Date(campaign.deadline) - new Date()) / (1000 * 60 * 60 * 24))}</span>
                        </div>
                    </div>
                </div>
                <div class="campaign-footer">
                    <span class="campaign-status ${campaign.status}">${campaign.status}</span>
                    <button class="btn btn-primary btn-sm" onclick="supportCampaign(${campaign.id})" style="margin-left: auto;">
                        <i class="fas fa-donate"></i> Support
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function renderActivities(activities = []) {
    const activityList = document.getElementById('activityList');
    if (!activityList) return;
    
    activityList.innerHTML = activities.map(activity => `
        <div class="activity-item">
            <div class="activity-icon">
                <i class="fas fa-${getActivityIcon(activity.activity_type)}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">${activity.description}</div>
                <div class="activity-time">${formatTimeElapsed(activity.created_at)}</div>
            </div>
        </div>
    `).join('');
}

function renderLeaderboard(leaderboard = []) {
    const leaderboardElement = document.getElementById('leaderboard');
    if (!leaderboardElement) return;
    
    leaderboardElement.innerHTML = leaderboard.map((farmer, index) => `
        <div class="leaderboard-item">
            <div class="leaderboard-rank">${index + 1}</div>
            <div class="leaderboard-info">
                <div class="leaderboard-name">${farmer.name}</div>
                <div class="leaderboard-details">${farmer.location} • ${farmer.experience} years experience</div>
            </div>
            <div class="leaderboard-yield">${farmer.yield} cavans/ha</div>
        </div>
    `).join('');
}

function renderKnowledge(knowledge = []) {
    const knowledgeGrid = document.getElementById('knowledgeGrid');
    if (!knowledgeGrid) return;
    
    knowledgeGrid.innerHTML = knowledge.map(item => `
        <div class="knowledge-item">
            <h4>${item.title}</h4>
            <p>${item.description}</p>
            <div class="knowledge-author">Shared by ${item.author}</div>
        </div>
    `).join('');
}

// Modal Functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Form Handlers with Database Integration
function handleAddFarmer(e) {
    e.preventDefault();
    console.log('Adding new farmer...');
    
    const formData = new FormData();
    formData.append('full_name', document.getElementById('farmerName').value);
    formData.append('experience_years', document.getElementById('farmerExperience').value);
    formData.append('location', document.getElementById('farmerLocation').value);
    formData.append('farm_size', document.getElementById('farmSize').value);
    formData.append('rice_varieties', Array.from(document.getElementById('riceVarieties').selectedOptions).map(opt => opt.value).join(','));
    formData.append('farming_method', document.getElementById('farmingMethod').value);
    formData.append('land_ownership', document.getElementById('landOwnership').value);
    
    console.log('Form data:', Object.fromEntries(formData));
    
    fetch('/farmstat/api/farmers', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Add farmer response:', data);
        if (data.success) {
            // Add the new farmer to the farmers array
            if (data.farmer) {
                appState.farmers.push(data.farmer);
                renderFarmers();
            }
            
            hideModal('addFarmerModal');
            showNotification('Farmer registered successfully!', 'success');
            e.target.reset();
        } else {
            showNotification(data.error || 'Failed to add farmer', 'error');
        }
    })
    .catch(error => {
        console.error('Error adding farmer:', error);
        showNotification('Error adding farmer: ' + error.message, 'error');
    });
}   

function handleCreateCampaign(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('title', document.getElementById('campaignTitle').value);
    formData.append('type', document.getElementById('campaignType').value);
    formData.append('goal_amount', document.getElementById('campaignGoal').value);
    formData.append('description', document.getElementById('campaignDescription').value);
    formData.append('deadline', document.getElementById('campaignDeadline').value);
    
    fetch('/farmstat/api/campaigns', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal('createCampaignModal');
            loadCampaignsData(); // Reload campaigns data
            showNotification('Campaign created successfully!', 'success');
            e.target.reset();
        } else {
            showNotification(data.error || 'Failed to create campaign', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating campaign', 'error');
    });
}

// Utility Functions
function getActivityIcon(activityType) {
    const icons = {
        'login': 'sign-in-alt',
        'user': 'user-plus',
        'campaign': 'hand-holding-usd',
        'system': 'chart-line',
        'alert': 'exclamation-triangle'
    };
    return icons[activityType] || 'circle';
}

function formatTimeElapsed(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffMins < 1) return 'just now';
    if (diffMins < 60) return `${diffMins} minutes ago`;
    if (diffHours < 24) return `${diffHours} hours ago`;
    if (diffDays < 7) return `${diffDays} days ago`;
    return date.toLocaleDateString();
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles for notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4a7c59' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Action Functions (called from HTML onclick)
function viewFarmerDetails(farmerId) {
    const farmer = appState.farmers.find(f => f.id === farmerId);
    if (farmer) {
        showNotification(`Viewing details for ${farmer.full_name}`, 'info');
        // In a real app, this would open a detailed view modal
    }
}

function supportFarmer(farmerId) {
    const farmer = appState.farmers.find(f => f.id === farmerId);
    if (farmer) {
        showNotification(`Support options for ${farmer.full_name}`, 'info');
        // In a real app, this would open support options
    }
}

function supportCampaign(campaignId) {
    const campaign = appState.campaigns.find(c => c.id === campaignId);
    if (campaign) {
        showNotification(`Supporting campaign: ${campaign.title}`, 'info');
        // In a real app, this would open a payment/donation modal
    }
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .notification-content i {
        font-size: 1.2rem;
    }
`;
document.head.appendChild(style);
document.addEventListener('DOMContentLoaded', initApp);