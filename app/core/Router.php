<?php
/**
 * Simple Router Class
 * PHP 8 Compatible
 */

class Router {
    private array $routes = [];
    private array $middlewares = [];

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
        $requestUri = str_replace('/farmstat', '', $requestUri);
        $requestUri = rtrim($requestUri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $requestUri)) {
                $this->executeRoute($route);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
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
            die("Controller not found: {$controllerName}");
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

