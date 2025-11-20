<?php
$title = 'Register - FarmStats';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="register-container">
    <div class="register-header">
        <div class="logo">
            <i class="fas fa-seedling"></i>
        </div>
        <h1>Join FarmStats</h1>
        <p>Create your account to get started</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <?php 
            echo htmlspecialchars($_SESSION['success']); 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>/register">
        <div class="user-type-selector">
            <div class="user-type-option">
                <input type="radio" id="admin" name="user_type" value="admin" required>
                <label for="admin" class="user-type-label">
                    <div class="user-type-icon admin-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="user-type-name">Administrator</div>
                    <div class="user-type-desc">System Management</div>
                </label>
            </div>
            
            <div class="user-type-option">
                <input type="radio" id="farmer" name="user_type" value="farmer" required checked>
                <label for="farmer" class="user-type-label">
                    <div class="user-type-icon client-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-type-name">Farmer</div>
                    <div class="user-type-desc">Campaign Farmer</div>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="name">Full Name</label>
            <div class="input-with-icon">
                <i class="fas fa-user"></i>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-with-icon">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Create a password" required minlength="6">
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-with-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <div id="password-match" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus"></i>
            Create Account
        </button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="<?php echo BASE_URL; ?>/login">Sign in here</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('password-match');

    confirmPasswordInput.addEventListener('input', function() {
        if (passwordInput.value === this.value) {
            passwordMatch.innerHTML = '<span style="color: #16a34a;"><i class="fas fa-check-circle"></i> Passwords match</span>';
        } else {
            passwordMatch.innerHTML = '<span style="color: #dc2626;"><i class="fas fa-exclamation-circle"></i> Passwords do not match</span>';
        }
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>

