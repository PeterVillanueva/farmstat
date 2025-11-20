<?php
/**
 * Base Controller Class
 * PHP 8 Compatible
 */

class Controller {
    protected PDO $db;

    public function __construct() {
        require_once APP_PATH . '/config/config.php';
        require_once APP_PATH . '/config/database.php';
        $this->db = Database::getConnection();
    }

    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View file not found: {$view}");
        }
        
        require_once $viewFile;
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        // If URL doesn't start with http or https, add BASE_URL prefix
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = BASE_URL . $url;
        }
        header("Location: {$url}");
        exit;
    }

    protected function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth(): void {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function requireRole(string $role): void {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== $role) {
            $this->redirect('/dashboard');
        }
    }
}

