<?php
$title = 'Campaign Management - FarmStats Admin';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="admin-page">
    <div class="page-header">
        <h1>Campaign Management</h1>
        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Create Campaign
            </button>
        </div>
    </div>

    <div class="stats-summary">
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['total_campaigns']; ?></span>
            <span class="stat-label">Total Campaigns</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['active_campaigns']; ?></span>
            <span class="stat-label">Active Campaigns</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">₱<?php echo number_format($stats['total_funding_goal'], 0); ?></span>
            <span class="stat-label">Total Goal</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">₱<?php echo number_format($stats['total_raised'], 0); ?></span>
            <span class="stat-label">Total Raised</span>
        </div>
    </div>

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Goal</th>
                    <th>Raised</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $campaign): ?>
                <?php 
                $progress = $campaign['funding_goal'] > 0 ? 
                    ($campaign['amount_raised'] / $campaign['funding_goal']) * 100 : 0;
                ?>
                <tr>
                    <td><?php echo $campaign['id']; ?></td>
                    <td><?php echo htmlspecialchars($campaign['title']); ?></td>
                    <td>
                        <span class="type-badge">
                            <?php echo htmlspecialchars($campaign['campaign_type'] ?? 'General'); ?>
                        </span>
                    </td>
                    <td>₱<?php echo number_format($campaign['funding_goal'], 0); ?></td>
                    <td>₱<?php echo number_format($campaign['amount_raised'], 0); ?></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(100, $progress); ?>%"></div>
                        </div>
                        <span class="progress-text"><?php echo number_format($progress, 1); ?>%</span>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $campaign['status']; ?>">
                            <?php echo ucfirst($campaign['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        echo $campaign['deadline'] ? 
                            date('M j, Y', strtotime($campaign['deadline'])) : 'N/A'; 
                        ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline">View</button>
                            <button class="btn btn-sm btn-primary">Edit</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
