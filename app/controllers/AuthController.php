<?php
/**
 * Authentication Controller
 * PHP 8 Compatible
 */

class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        parent::__construct();
        require_once MODELS_PATH . '/User.php';
        $this->userModel = new User();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user_type = $_POST['user_type'] ?? 'farmer';

            $user = $this->userModel->findByEmail($email, $user_type);

            if ($user && password_verify($password, $user['password'])) {
                $this->userModel->updateLastLogin($user['id']);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['last_login'] = $user['last_login'] ?? date('Y-m-d H:i:s');

                if ($user['role'] === 'admin') {
                    $this->redirect('/admin/dashboard');
                } else {
                    $this->redirect('/dashboard');
                }
            } else {
                $_SESSION['error'] = "Invalid email or password for the selected user type";
                $this->redirect('/login');
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $user_type = $_POST['user_type'] ?? 'farmer';

            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Passwords do not match!";
                $this->redirect('/register');
                return;
            }

            if ($this->userModel->emailExists($email)) {
                $_SESSION['error'] = "Email already exists!";
                $this->redirect('/register');
                return;
            }

            try {
                $this->userModel->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'role' => $user_type
                ]);

                $_SESSION['success'] = "Registration successful! Please login.";
                $this->redirect('/login');
            } catch (Exception $e) {
                $_SESSION['error'] = "Registration failed: " . $e->getMessage();
                $this->redirect('/register');
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('/login');
    }

    public function checkAuth(): void {
        header('Content-Type: application/json');
        
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'authenticated' => true,
                'user_id' => $_SESSION['user_id'],
                'user_name' => $_SESSION['user_name'] ?? '',
                'user_email' => $_SESSION['user_email'] ?? '',
                'user_role' => $_SESSION['user_role'] ?? 'farmer'
            ]);
        } else {
            echo json_encode([
                'authenticated' => false
            ]);
        }
    }
}

