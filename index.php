<?php
/**
 * Main Application Entry Point
 * MVC Router
 * PHP 8 Compatible
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Controller.php';

$router = new Router();

// Home routes
$router->get('/', 'HomeController', 'index');

// Auth routes
$router->get('/login', 'AuthController', 'login');
$router->post('/login', 'AuthController', 'login');
$router->get('/register', 'AuthController', 'register');
$router->post('/register', 'AuthController', 'register');
$router->get('/logout', 'AuthController', 'logout');

// Dashboard routes
$router->get('/dashboard', 'DashboardController', 'index');
$router->get('/admin/dashboard', 'DashboardController', 'admin');

// API routes
$router->get('/api/farmers', 'FarmerController', 'index');
$router->post('/api/farmers', 'FarmerController', 'create');
$router->get('/api/campaigns', 'CampaignController', 'index');
$router->post('/api/campaigns', 'CampaignController', 'create');

// Dispatch the request
$router->dispatch();

