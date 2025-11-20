<?php
/**
 * Simple Router Class
 * PHP 8 Compatible
 */

class Router {
    private array $routes = [];
    private array $middlewares = [];
    private bool $debug = false;

    public function __construct() {
        // Enable debug mode if in development
        $this->debug = defined('APP_DEBUG') ? APP_DEBUG : false;
    }

    public function get(string $path, string $controller, string $method = 'index'): void {
        $this->addRoute('GET', $path, $controller, $method);
    }

    public function post(string $path, string $controller, string $method = 'index'): void {
        $this->addRoute('POST', $path, $controller, $method);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if application is in a subdirectory
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }
        
        $requestUri = rtrim($requestUri, '/') ?: '/';

        if ($this->debug) {
            error_log("Router Debug - Request Method: $requestMethod");
            error_log("Router Debug - Request URI: $requestUri");
            error_log("Router Debug - Script Name: " . $_SERVER['SCRIPT_NAME']);
            error_log("Router Debug - Available routes: " . print_r($this->routes, true));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $requestUri)) {
                if ($this->debug) {
                    error_log("Router Debug - Route matched: " . $route['path']);
                }
                $this->executeRoute($route);
                return;
            }
        }

        // 404 Not Found - with better error message
        http_response_code(404);
        if ($this->debug) {
            echo "<h2>404 - Route Not Found</h2>";
            echo "<p><strong>Requested:</strong> $requestMethod $requestUri</p>";
            echo "<p><strong>Available routes:</strong></p><ul>";
            foreach ($this->routes as $route) {
                echo "<li>{$route['method']} {$route['path']} → {$route['controller']}::{$route['action']}</li>";
            }
            echo "</ul>";
            echo "<hr><p><strong>Tip:</strong> Make sure you're accessing the correct URL with the /farmstat/ prefix:</p>";
            echo "<ul>";
            echo "<li><a href='/farmstat/'>Home</a></li>";
            echo "<li><a href='/farmstat/login'>Login</a></li>";
            echo "<li><a href='/farmstat/admin/dashboard'>Admin Dashboard</a></li>";
            echo "</ul>";
        } else {
            echo "404 - Page Not Found";
        }
    }

    private function matchRoute(string $routePath, string $requestUri): bool {
        $routePath = rtrim($routePath, '/') ?: '/';
        return $routePath === $requestUri;
    }

    private function executeRoute(array $route): void {
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        
        $controllerFile = CONTROLLERS_PATH . '/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            die("Controller not found: {$controllerName} at {$controllerFile}");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            die("Controller class not found: {$controllerName}");
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $actionName)) {
            die("Method not found: {$controllerName}::{$actionName}");
        }
        
        $controller->$actionName();
    }
}