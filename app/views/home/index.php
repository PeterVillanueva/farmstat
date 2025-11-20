<?php
$title = 'FarmStat - Rice Farming Intelligence & Crowdfunding';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div id="landingPage">
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <span class="logo">🌾</span>
                <span>FarmStat</span>
            </div>
            <div class="nav-actions">
                <button id="getStartedBtn" class="btn btn-primary">Get Started</button>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Rice Farming Intelligence</h1>
                <p class="hero-subtitle">Empowering Filipino rice farmers with digital profiling and community-backed funding while creating the most detailed agricultural database in the Philippines.</p>
                <div class="hero-actions">
                    <button id="heroGetStarted" class="btn btn-primary">Start Your Journey</button>
                    <button id="heroDemo" class="btn btn-outline">See Platform in Action</button>
                </div>
                <div class="impact-stats">
                    <div class="impact-stat">
                        <span class="impact-number">2,847</span>
                        <span class="impact-label">Rice Farmers</span>
                    </div>
                    <div class="impact-stat">
                        <span class="impact-number">₱18.2M</span>
                        <span class="impact-label">Community Funding</span>
                    </div>
                    <div class="impact-stat">
                        <span class="impact-number">156%</span>
                        <span class="impact-label">Avg. Yield Increase</span>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="data-card">
                    <div class="card-header">
                        <span class="card-icon">📊</span>
                        <h3>Season 2024 Progress</h3>
                    </div>
                    <div class="card-stats">
                        <div class="stat">
                            <span class="stat-value">68%</span>
                            <span class="stat-label">In Growth Phase</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">24%</span>
                            <span class="stat-label">Ready for Harvest</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="mission-section">
            <div class="container">
                <div class="mission-content">
                    <h2>Our Mission</h2>
                    <p class="mission-statement">"Empowering every Filipino rice farmer with digital profiling and community-backed funding while creating the most detailed agricultural database in the Philippines."</p>
                    <div class="mission-pillars">
                        <div class="pillar">
                            <i class="fas fa-database"></i>
                            <h4>Comprehensive Data</h4>
                            <p>Detailed agricultural monitoring and intelligence</p>
                        </div>
                        <div class="pillar">
                            <i class="fas fa-hand-holding-heart"></i>
                            <h4>Community Support</h4>
                            <p>Transparent crowdfunding with impact tracking</p>
                        </div>
                        <div class="pillar">
                            <i class="fas fa-chart-line"></i>
                            <h4>Performance Analytics</h4>
                            <p>Multi-season comparison and improvement tracking</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <h2>Revolutionizing Rice Farming</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">👨‍🌾</div>
                        <h3>Farmer Profiles 2.0</h3>
                        <p>Complete agricultural journey tracking with GPS mapping, farming experience, and family involvement documentation</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🌱</div>
                        <h3>Seasonal Monitoring</h3>
                        <p>Comprehensive rice farming analytics from planting to harvest with real-time progress tracking</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📈</div>
                        <h3>Historical Analytics</h3>
                        <p>5-year farming timeline with yield trends, climate impact analysis, and technology adoption journey</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🤝</div>
                        <h3>Community Intelligence</h3>
                        <p>Compare yields, share best practices, and learn from successful techniques across the community</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💰</div>
                        <h3>Impact Crowdfunding</h3>
                        <p>Project-based funding with transparent ROI tracking from donation to harvest results</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3>Mobile Field Monitoring</h3>
                        <p>Photo documentation, pest reporting, and offline capability for remote farming areas</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rice Farming Intelligence Section -->
        <section class="rice-intelligence">
            <div class="container">
                <h2>Rice Farming Intelligence System</h2>
                <div class="intelligence-grid">
                    <div class="intelligence-card">
                        <h3>🌾 Rice Varieties & Methods</h3>
                        <ul>
                            <li>Primary varieties: Jasmine, Sinandomeng, IR64</li>
                            <li>Farming methods: Traditional, Modern, Organic</li>
                            <li>Land ownership tracking</li>
                            <li>Soil health assessment</li>
                        </ul>
                    </div>
                    <div class="intelligence-card">
                        <h3>📅 Seasonal Monitoring</h3>
                        <div class="phase-timeline">
                            <div class="phase active">
                                <span class="phase-name">Planting</span>
                                <span class="phase-date">Jan-Mar 2024</span>
                            </div>
                            <div class="phase active">
                                <span class="phase-name">Growth</span>
                                <span class="phase-date">Apr-Jul 2024</span>
                            </div>
                            <div class="phase">
                                <span class="phase-name">Harvest</span>
                                <span class="phase-date">Aug-Oct 2024</span>
                            </div>
                        </div>
                    </div>
                    <div class="intelligence-card">
                        <h3>🐄 Livestock Integration</h3>
                        <ul>
                            <li>Carabao/draft animal management</li>
                            <li>Rice-poultry synergy systems</li>
                            <li>Feed sources and care tracking</li>
                            <li>Integration with rice fields</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Join the Agricultural Revolution</h2>
                    <p>Be part of creating a future where every Filipino rice farmer has the tools, community support, and financial backing to thrive.</p>
                    <div class="cta-actions">
                        <button id="ctaGetStarted" class="btn btn-primary">Get Started Now</button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get Started button handlers
    const getStartedButtons = [
        document.getElementById('getStartedBtn'),
        document.getElementById('heroGetStarted'),
        document.getElementById('ctaGetStarted')
    ];

    getStartedButtons.forEach(button => {
        if (button) {
            button.addEventListener('click', function() {
                window.location.href = '<?php echo BASE_URL; ?>/login';
            });
        }
    });

    // Demo button handler
    const demoButton = document.getElementById('heroDemo');
    if (demoButton) {
        demoButton.addEventListener('click', function() {
            // Scroll to features section for demo
            document.querySelector('.features').scrollIntoView({ 
                behavior: 'smooth' 
            });
        });
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>