<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists!";
        header("Location: register.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert user with role - SIMPLIFIED without activity logging
        $sql = "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            throw new Exception("Database insertion failed");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FarmStats</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        .register-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--accent-gold));
        }

        .register-header {
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

        .register-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .register-header p {
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

        .password-requirements {
            background: var(--background);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .password-requirements ul {
            list-style: none;
            margin-left: 0.5rem;
        }

        .password-requirements li {
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .requirement-met {
            color: var(--primary-medium);
        }

        .requirement-unmet {
            color: var(--text-light);
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

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid rgba(34, 197, 94, 0.2);
            font-weight: 500;
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .login-link p {
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .login-link a {
            color: var(--primary-medium);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 2rem 1.5rem;
            }
            
            .user-type-selector {
                grid-template-columns: 1fr;
            }
            
            .register-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
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
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
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

            <!-- Full Name Field -->
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
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
                    <input type="password" id="password" name="password" placeholder="Create a password" required minlength="6">
                </div>
                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <ul>
                        <li id="req-length" class="requirement-unmet">
                            <i class="fas fa-circle"></i>
                            At least 6 characters
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <div id="password-match" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Create Account
            </button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordMatch = document.getElementById('password-match');
            const reqLength = document.getElementById('req-length');

            // Password length requirement
            passwordInput.addEventListener('input', function() {
                if (this.value.length >= 6) {
                    reqLength.className = 'requirement-met';
                    reqLength.innerHTML = '<i class="fas fa-check-circle"></i> At least 6 characters';
                } else {
                    reqLength.className = 'requirement-unmet';
                    reqLength.innerHTML = '<i class="fas fa-circle"></i> At least 6 characters';
                }
            });

            // Password match validation
            confirmPasswordInput.addEventListener('input', function() {
                if (passwordInput.value === this.value) {
                    passwordMatch.innerHTML = '<span style="color: #16a34a;"><i class="fas fa-check-circle"></i> Passwords match</span>';
                } else {
                    passwordMatch.innerHTML = '<span style="color: #dc2626;"><i class="fas fa-exclamation-circle"></i> Passwords do not match</span>';
                }
            });

            // Auto-select client by default
            document.getElementById('client').checked = true;

            // Add interactive effects
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>