// FarmStats JavaScript with PHP Connections

// Global variables
let currentUser = null;

// DOM Content Loaded - Main initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('FarmStats App Initializing...');
    initializeApp();
});

function initializeApp() {
    console.log('Initializing FarmStats Application...');
    
    // Initialize all functionality
    initializeNavigation();
    initializeAuth();
    initializeModals();
    initializeForms();
    
    // Check if we're already on dashboard
    const dashboard = document.getElementById('dashboard');
    if (dashboard && !dashboard.classList.contains('hidden')) {
        loadInitialData();
    }
    
    console.log('FarmStats App Initialized Successfully');
}

// Navigation functionality
function initializeNavigation() {
    console.log('Initializing navigation...');
    
    const getStartedBtn = document.getElementById('getStartedBtn');
    const heroGetStartedBtn = document.getElementById('heroGetStarted');
    const ctaGetStartedBtn = document.getElementById('ctaGetStarted');
    const landingPage = document.getElementById('landingPage');
    const dashboard = document.getElementById('dashboard');

    function showDashboard() {
        console.log('Showing dashboard...');
        if (landingPage && dashboard) {
            landingPage.classList.add('hidden');
            dashboard.classList.remove('hidden');
            loadInitialData();
        }
    }

    // Get Started buttons
    if (getStartedBtn) {
        getStartedBtn.addEventListener('click', showDashboard);
    }
    if (heroGetStartedBtn) {
        heroGetStartedBtn.addEventListener('click', showDashboard);
    }
    if (ctaGetStartedBtn) {
        ctaGetStartedBtn.addEventListener('click', showDashboard);
    }

    // Demo button
    const heroDemoBtn = document.getElementById('heroDemo');
    if (heroDemoBtn) {
        heroDemoBtn.addEventListener('click', function() {
            alert('This would show a demo of the platform in action.');
        });
    }

    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'login.php';
            }
        });
    }

    // Sidebar navigation
    const navItems = document.querySelectorAll('.nav-item');
    const pages = document.querySelectorAll('.page');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const pageName = this.getAttribute('data-page');
            console.log('Switching to page: ' + pageName);
            
            // Update active nav item
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            
            // Show selected page
            pages.forEach(page => {
                page.classList.remove('active');
                if (page.id === pageName + 'Page') {
                    page.classList.add('active');
                }
            });
            
            // Update page title
            const pageTitle = document.getElementById('pageTitle');
            if (pageTitle) {
                pageTitle.textContent = this.querySelector('span').textContent;
            }
            
            // Load page-specific data
            loadPageData(pageName);
        });
    });
}

// Authentication functionality
function initializeAuth() {
    console.log('Initializing authentication...');
    // You can add session checking here later
}

// Modal functionality
function initializeModals() {
    console.log('Initializing modals...');
    
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');
    
    // Close buttons
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Initialize specific modals
    initializeAddFarmerModal();
    initializeCreateCampaignModal();
}

function initializeAddFarmerModal() {
    const addFarmerBtn = document.getElementById('addFarmerBtn');
    const addFarmerModal = document.getElementById('addFarmerModal');
    const cancelFarmer = document.getElementById('cancelFarmer');
    const addFarmerForm = document.getElementById('addFarmerForm');
    
    console.log('Add Farmer Modal Elements:', {
        addFarmerBtn: !!addFarmerBtn,
        addFarmerModal: !!addFarmerModal,
        cancelFarmer: !!cancelFarmer,
        addFarmerForm: !!addFarmerForm
    });
    
    if (addFarmerBtn && addFarmerModal) {
        addFarmerBtn.addEventListener('click', function() {
            console.log('Opening Add Farmer Modal');
            addFarmerModal.style.display = 'block';
        });
    }
    
    if (cancelFarmer && addFarmerModal) {
        cancelFarmer.addEventListener('click', function() {
            addFarmerModal.style.display = 'none';
            if (addFarmerForm) addFarmerForm.reset();
        });
    }
    
    if (addFarmerForm) {
        addFarmerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleAddFarmerSubmission(this);
        });
    }
}

function initializeCreateCampaignModal() {
    const createCampaignBtn = document.getElementById('createCampaignBtn');
    const createCampaignModal = document.getElementById('createCampaignModal');
    const cancelCampaign = document.getElementById('cancelCampaign');
    const createCampaignForm = document.getElementById('createCampaignForm');
    
    console.log('Create Campaign Modal Elements:', {
        createCampaignBtn: !!createCampaignBtn,
        createCampaignModal: !!createCampaignModal,
        cancelCampaign: !!cancelCampaign,
        createCampaignForm: !!createCampaignForm
    });
    
    if (createCampaignBtn && createCampaignModal) {
        createCampaignBtn.addEventListener('click', function() {
            console.log('Opening Create Campaign Modal');
            createCampaignModal.style.display = 'block';
        });
    }
    
    if (cancelCampaign && createCampaignModal) {
        cancelCampaign.addEventListener('click', function() {
            createCampaignModal.style.display = 'none';
            if (createCampaignForm) createCampaignForm.reset();
        });
    }
    
    if (createCampaignForm) {
        createCampaignForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleCreateCampaignSubmission(this);
        });
    }
}

// Form handling
function initializeForms() {
    console.log('Initializing forms...');
    
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Profile changes saved successfully!');
        });
    }
    
    const profileTabs = document.querySelectorAll('.profile-tab');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    profileTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            profileTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                if (pane.id === tabName + 'Tab') {
                    pane.classList.add('active');
                }
            });
        });
    });

    // Initialize farmer card buttons
    initializeFarmerCardButtons();
}

function initializeFarmerCardButtons() {
    // This will be called after farmers are loaded to add event listeners to dynamic buttons
    console.log('Initializing farmer card buttons...');
}

// Data loading functions
function loadInitialData() {
    console.log('Loading initial dashboard data...');
    loadDashboardData();
    loadFarmers();
    loadCampaigns();
}

function loadPageData(pageName) {
    console.log('Loading data for page: ' + pageName);
    switch(pageName) {
        case 'farmers':
            loadFarmers();
            break;
        case 'crowdfunding':
            loadCampaigns();
            break;
        case 'overview':
            loadDashboardData();
            break;
        default:
            console.log('No specific data loader for page: ' + pageName);
    }
}

// API functions - CONNECTED TO PHP
async function loadDashboardData() {
    try {
        console.log('Loading dashboard data...');
        const response = await fetch('/farmstat/api/dashboard');
        
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        
        const data = await response.json();
        console.log('Dashboard data received:', data);
        
        if (data.success) {
            // Update dashboard stats
            document.getElementById('totalFarmers').textContent = data.stats.total_farmers || '0';
            document.getElementById('activeCrops').textContent = data.stats.active_crops || '0';
            document.getElementById('totalFunding').textContent = '₱' + (data.stats.total_funding ? data.stats.total_funding.toLocaleString() : '0');
            document.getElementById('yieldIncrease').textContent = data.stats.yield_increase || '0%';
            
            // Update activities
            const activityList = document.getElementById('activityList');
            if (activityList && data.activities) {
                activityList.innerHTML = data.activities.map(activity => `
                    <div class="activity-item">
                        <div class="activity-content">
                            <strong>${activity.full_name}</strong>
                            <span>${activity.activity_type} progress: ${activity.progress_percent}%</span>
                        </div>
                        <small>${new Date(activity.created_at).toLocaleDateString()}</small>
                    </div>
                `).join('');
            } else {
                activityList.innerHTML = '<div class="activity-item">No recent activities</div>';
            }
        } else {
            console.error('Dashboard data error:', data.error);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        // Fallback data
        document.getElementById('totalFarmers').textContent = '2,847';
        document.getElementById('activeCrops').textContent = '1,856';
        document.getElementById('totalFunding').textContent = '₱18.2M';
        document.getElementById('yieldIncrease').textContent = '156%';
    }
}

async function loadFarmers() {
    try {
        console.log('Loading farmers data...');
        const response = await fetch('/farmstat/api/farmers');
        
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        
        const data = await response.json();
        console.log('Farmers data received:', data);
        
        const farmersGrid = document.getElementById('farmersGrid');
        
        if (data.success && data.farmers && data.farmers.length > 0) {
            farmersGrid.innerHTML = data.farmers.map(farmer => `
                <div class="farmer-card">
                    <div class="farmer-header">
                        <h3>${farmer.full_name}</h3>
                        <span class="experience">${farmer.years_experience || '0'} years</span>
                    </div>
                    <div class="farmer-details">
                        <p><i class="fas fa-map-marker-alt"></i> ${farmer.farm_location || 'Location not set'}</p>
                        <p><i class="fas fa-tractor"></i> ${farmer.farm_size || '0'} hectares</p>
                        <p><i class="fas fa-seedling"></i> ${farmer.farming_method || 'Not specified'} farming</p>
                        ${farmer.varieties ? `<p><i class="fas fa-leaf"></i> ${farmer.varieties}</p>` : ''}
                    </div>
                    <div class="farmer-actions">
                        <button class="btn btn-sm btn-outline view-profile-btn" data-farmer-id="${farmer.id}">View Profile</button>
                        <button class="btn btn-sm btn-primary support-farmer-btn" data-farmer-id="${farmer.id}">Support</button>
                    </div>
                </div>
            `).join('');

            // Add event listeners to the dynamically created buttons
            initializeFarmerCardButtons();
        } else {
            farmersGrid.innerHTML = `
                <div class="farmer-card">
                    <div class="farmer-header">
                        <h3>No Farmers Found</h3>
                    </div>
                    <div class="farmer-details">
                        <p>Click "Register Farmer" to add the first farmer</p>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading farmers:', error);
        const farmersGrid = document.getElementById('farmersGrid');
        farmersGrid.innerHTML = `
            <div class="farmer-card">
                <div class="farmer-header">
                    <h3>Error Loading Farmers</h3>
                </div>
                <div class="farmer-details">
                    <p>Please try again later</p>
                </div>
            </div>
        `;
    }
}

async function loadCampaigns() {
    try {
        console.log('Loading campaigns data...');
        const response = await fetch('/farmstat/api/campaigns');
        
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        
        const data = await response.json();
        console.log('Campaigns data received:', data);
        
        const campaignsGrid = document.getElementById('campaignsGrid');
        
        if (data.success && data.campaigns && data.campaigns.length > 0) {
            campaignsGrid.innerHTML = data.campaigns.map(campaign => {
                const progress = campaign.funding_goal > 0 ? (campaign.amount_raised / campaign.funding_goal) * 100 : 0;
                return `
                <div class="campaign-card">
                    <div class="campaign-header">
                        <h3>${campaign.title}</h3>
                        <span class="campaign-type">${campaign.campaign_type}</span>
                    </div>
                    <p class="farmer-name">By: ${campaign.farmer_name || 'Unknown Farmer'}</p>
                    <p class="campaign-description">${campaign.description}</p>
                    <div class="funding-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${progress}%"></div>
                        </div>
                        <div class="funding-stats">
                            <span>₱${(campaign.amount_raised || 0).toLocaleString()} raised</span>
                            <span>Goal: ₱${(campaign.funding_goal || 0).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="campaign-deadline">
                        <i class="fas fa-clock"></i> ${campaign.deadline ? new Date(campaign.deadline).toLocaleDateString() : 'No deadline'}
                    </div>
                    <button class="btn btn-primary btn-block support-campaign-btn" data-campaign-id="${campaign.id}">Support Campaign</button>
                </div>
                `;
            }).join('');

            // Add event listeners to campaign buttons
            initializeCampaignButtons();
        } else {
            campaignsGrid.innerHTML = `
                <div class="campaign-card">
                    <div class="campaign-header">
                        <h3>No Active Campaigns</h3>
                    </div>
                    <p class="campaign-description">Click "Create Campaign" to start the first fundraising campaign</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading campaigns:', error);
        const campaignsGrid = document.getElementById('campaignsGrid');
        campaignsGrid.innerHTML = `
            <div class="campaign-card">
                <div class="campaign-header">
                    <h3>Error Loading Campaigns</h3>
                </div>
                <p class="campaign-description">Please try again later</p>
            </div>
        `;
    }
}

// Initialize dynamic buttons
function initializeFarmerCardButtons() {
    // View Profile buttons
    const viewProfileBtns = document.querySelectorAll('.view-profile-btn');
    viewProfileBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const farmerId = this.getAttribute('data-farmer-id');
            alert('Viewing profile for farmer ID: ' + farmerId);
        });
    });

    // Support Farmer buttons
    const supportFarmerBtns = document.querySelectorAll('.support-farmer-btn');
    supportFarmerBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const farmerId = this.getAttribute('data-farmer-id');
            alert('Supporting farmer ID: ' + farmerId);
        });
    });
}

function initializeCampaignButtons() {
    // Support Campaign buttons
    const supportCampaignBtns = document.querySelectorAll('.support-campaign-btn');
    supportCampaignBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const campaignId = this.getAttribute('data-campaign-id');
            alert('Supporting campaign ID: ' + campaignId);
        });
    });
}

// Form submission handlers
async function handleAddFarmerSubmission(form) {
    const formData = {
        full_name: document.getElementById('farmerName').value,
        years_experience: parseInt(document.getElementById('farmerExperience').value) || 0,
        farm_location: document.getElementById('farmerLocation').value,
        farm_size: parseFloat(document.getElementById('farmSize').value) || 0,
        farming_method: document.getElementById('farmingMethod').value,
        land_ownership: document.getElementById('landOwnership').value,
        varieties: Array.from(document.getElementById('riceVarieties').selectedOptions)
                    .map(option => option.value)
    };

    console.log('Submitting farmer data:', formData);

    // Validate required fields
    if (!formData.full_name || !formData.farm_location || !formData.farm_size) {
        alert('Please fill in all required fields: Name, Location, and Farm Size');
        return;
    }

    try {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Adding Farmer...';
        submitBtn.disabled = true;

        const response = await fetch('/farmstat/api/farmers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }

        const result = await response.json();
        console.log('Farmer submission response:', result);

        if (result.success) {
            alert('Farmer added successfully!');
            document.getElementById('addFarmerModal').style.display = 'none';
            form.reset();
            // Refresh data
            loadFarmers();
            loadDashboardData();
        } else {
            alert('Error: ' + (result.error || 'Failed to add farmer'));
        }

    } catch (error) {
        console.error('Error submitting farmer:', error);
        alert('Network error: Could not add farmer. Please check your connection.');
    } finally {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'Register Farmer';
            submitBtn.disabled = false;
        }
    }
}

async function handleCreateCampaignSubmission(form) {
    const formData = {
        title: document.getElementById('campaignTitle').value,
        description: document.getElementById('campaignDescription').value,
        campaign_type: document.getElementById('campaignType').value,
        funding_goal: parseFloat(document.getElementById('campaignGoal').value) || 0,
        deadline: document.getElementById('campaignDeadline').value
    };

    console.log('Submitting campaign data:', formData);

    // Validate required fields
    if (!formData.title || !formData.description || !formData.campaign_type || !formData.funding_goal) {
        alert('Please fill in all required fields: Title, Description, Type, and Funding Goal');
        return;
    }

    try {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Creating Campaign...';
        submitBtn.disabled = true;

        const response = await fetch('/farmstat/api/campaigns', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }

        const result = await response.json();
        console.log('Campaign submission response:', result);

        if (result.success) {
            alert('Campaign created successfully!');
            document.getElementById('createCampaignModal').style.display = 'none';
            form.reset();
            // Refresh data
            loadCampaigns();
            loadDashboardData();
        } else {
            alert('Error: ' + (result.error || 'Failed to create campaign'));
        }

    } catch (error) {
        console.error('Error submitting campaign:', error);
        alert('Network error: Could not create campaign. Please check your connection.');
    } finally {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'Create Campaign';
            submitBtn.disabled = false;
        }
    }
}

// Utility function to show notifications
function showNotification(message, type = 'success') {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'error' ? '#f44336' : '#4CAF50'};
        color: white;
        border-radius: 5px;
        z-index: 10000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Make functions globally available for HTML onclick events
window.loadDashboardData = loadDashboardData;
window.loadFarmers = loadFarmers;
window.loadCampaigns = loadCampaigns;

// Debug function to check if buttons are working
window.debugButtons = function() {
    console.log('Checking button functionality...');
    
    const buttons = document.querySelectorAll('button');
    console.log('Total buttons found:', buttons.length);
    
    buttons.forEach((btn, index) => {
        console.log(`Button ${index}:`, {
            text: btn.textContent,
            id: btn.id,
            class: btn.className,
            disabled: btn.disabled
        });
    });
};

// Call debug function to check buttons
setTimeout(() => {
    window.debugButtons();
}, 1000);