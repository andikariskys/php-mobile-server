<?php
/**
 * Entry Point for PHP Mobile Web Server
 * Handles routing via $_GET['page'] query parameter.
 */
session_start();

// Include database helper
require_once __DIR__ . '/db.php';

// Include and instantiate DeviceController
require_once __DIR__ . '/controllers/DeviceController.php';
$device = new DeviceController(true, true); // root/su enabled, termux api enabled


// Get page from query string
$page = isset($_GET['page']) ? trim($_GET['page']) : '';

// Map page query parameters to files
$routes = [
    'login' => 'pages/login.php',
    'dashboard' => 'pages/dashboard.php',
    'file-manager' => 'pages/file-manager.php',
    'device-control' => 'pages/device-control.php',
    'network' => 'pages/network.php',
    'sms' => 'pages/sms.php',
    'notification' => 'pages/notification.php',
    'camera' => 'pages/camera.php',
    'audio' => 'pages/audio.php',
    'location' => 'pages/location.php',
    'terminal' => 'pages/terminal.php',
    'developer' => 'pages/developer.php',
    'settings' => 'pages/settings.php'
];

// Helper to check authentication
function check_auth() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header("Location: index.php?page=login");
        exit;
    }
}

// Handle root redirect
if ($page === '') {
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        header("Location: index.php?page=dashboard");
    } else {
        header("Location: index.php?page=login");
    }
    exit;
}

// Handle logout
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// Define $uri so layout.php, is_active(), and topbar titles work out of the box
$uri = '/' . $page;

if (isset($routes[$page])) {
    $handler = $routes[$page];
    
    // Authenticate all routes except login
    if ($page !== 'login') {
        check_auth();
    }
    
    if ($page === 'login' || (isset($_GET['api']) && $_GET['api'] == '1')) {
        include $handler;
    } else {
        $page_content_file = $handler;
        include 'layout.php';
    }
} else {
    // 404 Not Found
    http_response_code(404);
    $page_content_file = 'pages/404.php';
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        include 'layout.php';
    } else {
        include 'pages/404.php';
    }
}
