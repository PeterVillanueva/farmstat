<?php
$title = 'Farmer Management - FarmStats Admin';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="admin-page">
    <div class="page-header">
        <h1>Farmer Management</h1>
        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New Farmer
            </button>
        </div>
    </div>

    <div class="stats-summary">
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['total_farmers']; ?></span>
            <span class="stat-label">Total Farmers</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['avg_experience']; ?> yrs</span>
            <span class="stat-label">Avg Experience</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo number_format($stats['total_farm_size'], 1); ?> ha</span>
            <span class="stat-label">Total Farm Size</span>
        </div>
    </div>

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Farm Size</th>
                    <th>Experience</th>
                    <th>Method</th>
                    <th>Varieties</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($farmers as $farmer): ?>
                <tr>
                    <td><?php echo $farmer['id']; ?></td>
                    <td><?php echo htmlspecialchars($farmer['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($farmer['farm_location'] ?? 'N/A'); ?></td>
                    <td><?php echo $farmer['farm_size'] ? number_format($farmer['farm_size'], 1) . ' ha' : 'N/A'; ?></td>
                    <td><?php echo $farmer['years_experience']; ?> years</td>
                    <td>
                        <span class="method-badge">
                            <?php echo htmlspecialchars($farmer['farming_method'] ?? 'N/A'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($farmer['varieties'] ?? 'N/A'); ?></td>
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
