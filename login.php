<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; // admin or client
    
    // Debug: Check what's being received
    error_log("Login attempt - Email: $email, User Type: $user_type");
    
    // First, let's check what roles exist in the database for this email
    $check_roles_sql = "SELECT role FROM users WHERE email = ?";
    $check_roles_stmt = $conn->prepare($check_roles_sql);
    $check_roles_stmt->bind_param("s", $email);
    $check_roles_stmt->execute();
    $roles_result = $check_roles_stmt->get_result();
    
    $available_roles = [];
    while ($row = $roles_result->fetch_assoc()) {
        $available_roles[] = $row['role'];
    }
    error_log("Available roles for $email: " . implode(', ', $available_roles));
    
    // Check user credentials with the selected role
    $sql = "SELECT * FROM users WHERE email = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("Found users with role $user_type: " . $result->num_rows);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        error_log("User found: " . $user['name'] . ", Role: " . $user['role']);
        
        if (password_verify($password, $user['password'])) {
            error_log("Password verified successfully");
            
            // Update last login and login count
            $update_sql = "UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['last_login'] = $user['last_login'];
            $_SESSION['user_email'] = $user['email'];
            
            // CHANGED THIS LINE: Farmers now go to index.php instead of dashboard.php
            error_log("Session set, redirecting to: " . ($user['role'] == 'admin' ? 'admin-dashboard.php' : 'index.php'));
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: index.html"); // CHANGED FROM dashboard.php TO index.php
            }
            exit();
        } else {
            error_log("Password verification failed");
            $_SESSION['error'] = "Invalid password for the selected user type";
        }
    } else {
        error_log("No user found with email: $email and role: $user_type");
        
        // Provide more helpful error message
        if (!empty($available_roles)) {
            $_SESSION['error'] = "Email exists but not as " . ($user_type == 'admin' ? 'Administrator' : 'Farmer') . 
                                ". Available role(s): " . implode(', ', $available_roles);
        } else {
            $_SESSION['error'] = "No account found with this email";
        }
    }
    
    // If login fails
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FarmStats</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS styles remain exactly the same */
        :root {
            --primary-dark: #1a4d2e;
            --primary-medium: #4a7c59;
            --primary-light: #8db596;
            --accent-gold: #d4af37;
            --accent-orange: #ff9a3d;
            --background: #f5f9f7;
            --white: #ffffff;
            --text: #2d3a2d;
            --text-light: #5a6c5a;
            --border: #dde8e0;
            --shadow: 0 4px 12px rgba(26, 77, 46, 0.08);
            --shadow-lg: 0 8px 24px rgba(26, 77, 46, 0.12);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-medium) 50%, var(--primary-dark) 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--accent-gold));
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-light);
            font-size: 1rem;
        }

        .user-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .user-type-option {
            position: relative;
        }

        .user-type-option input {
            display: none;
        }

        .user-type-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 1rem;
            border: 2px solid var(--border);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            background: var(--white);
        }

        .user-type-label:hover {
            border-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .user-type-option input:checked + .user-type-label {
            border-color: var(--primary-medium);
            background: linear-gradient(135deg, rgba(74, 124, 89, 0.05), rgba(141, 181, 150, 0.1));
            box-shadow: var(--shadow);
        }

        .user-type-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .admin-icon {
            background: linear-gradient(135deg, var(--primary-dark), var(--accent-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .client-icon {
            background: linear-gradient(135deg, var(--primary-medium), var(--accent-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-type-name {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 0.25rem;
        }

        .user-type-desc {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 0.95rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .input-with-icon input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
            font-family: inherit;
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: var(--primary-medium);
            box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        }

        .btn {
            width: 100%;
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-dark));
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 124, 89, 0.3);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-weight: 500;
        }

        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .register-link p {
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .register-link a {
            color: var(--primary-medium);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .register-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .user-type-selector {
                grid-template-columns: 1fr;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
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
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <!-- User Type Selection -->
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
                    <label for="client" class="user-type-label">
                        <div class="user-type-icon client-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-type-name">Farmer</div>
                        <div class="user-type-desc">Campaign Farmer</div>
                    </label>
                </div>
            </div>

            <!-- Email Field -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Create one here</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add some interactive effects
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Auto-select first user type if none selected
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
</body>
</html>