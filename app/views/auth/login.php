<?php
$title = 'Login - FarmStats';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="login-container">
    <div class="login-header">
        <div class="logo">
            <i class="fas fa-seedling"></i>
        </div>
        <h1>Welcome to FarmStats</h1>
        <p>Sign in to your account</p>
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

    <form method="POST" action="/login">
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
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
        </button>
    </form>

    <div class="register-link">
        <p>Don't have an account? <a href="/register">Create one here</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    const userTypeRadios = document.querySelectorAll('input[name="user_type"]');
    let hasSelection = false;
    userTypeRadios.forEach(radio => {
        if (radio.checked) hasSelection = true;
    });
    
    if (!hasSelection && userTypeRadios.length > 0) {
        userTypeRadios[0].checked = true;
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>

