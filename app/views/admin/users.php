<?php
$title = 'User Management - FarmStats Admin';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="admin-page">
    <div class="page-header">
        <h1>User Management</h1>
        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Add New User
            </button>
        </div>
    </div>

    <div class="stats-summary">
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['total_users']; ?></span>
            <span class="stat-label">Total Users</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['admin_count']; ?></span>
            <span class="stat-label">Administrators</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['farmer_count']; ?></span>
            <span class="stat-label">Farmers</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo $stats['active_count']; ?></span>
            <span class="stat-label">Active Users</span>
        </div>
    </div>

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="role-badge role-<?php echo $user['role']; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $user['status']; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
