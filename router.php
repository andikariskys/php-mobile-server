<?php
session_start();

// Include database helper
require_once __DIR__ . '/db.php';

// Get the requested URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Serve static files directly if they exist
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    // Let PHP built-in server serve static assets
    return false;
}

// 2. Define routes registry
$routes = [];

function get($route, $handler) {
    global $routes;
    $routes['GET'][$route] = $handler;
}

function post($route, $handler) {
    global $routes;
    $routes['POST'][$route] = $handler;
}

// Helper to check authentication
function check_auth() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header("Location: /login");
        exit;
    }
}

// Custom route matching with parameters
function match_route($method, $uri) {
    global $routes;
    if (!isset($routes[$method])) {
        return false;
    }
    
    foreach ($routes[$method] as $route => $handler) {
        // Convert route like /route/{id} to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return [
                'handler' => $handler,
                'params' => $params
            ];
        }
    }
    return false;
}

// Define available routes
get('/', function() {
    if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
        header("Location: /dashboard");
    } else {
        header("Location: /login");
    }
    exit;
});

get('/index.php', function() {
    header("Location: /");
    exit;
});

get('/login', 'pages/login.php');
post('/login', 'pages/login.php');

get('/logout', function() {
    session_destroy();
    header("Location: /login");
    exit;
});

get('/dashboard', 'pages/dashboard.php');
get('/file-manager', 'pages/file-manager.php');

get('/device-control', 'pages/device-control.php');
post('/device-control', 'pages/device-control.php');

get('/network', 'pages/network.php');
post('/network', 'pages/network.php');

get('/sms', 'pages/sms.php');
post('/sms', 'pages/sms.php');

get('/notification', 'pages/notification.php');
post('/notification', 'pages/notification.php');

get('/camera', 'pages/camera.php');
post('/camera', 'pages/camera.php');

get('/audio', 'pages/audio.php');
post('/audio', 'pages/audio.php');

get('/location', 'pages/location.php');

get('/terminal', 'pages/terminal.php');

get('/developer', 'pages/developer.php');

get('/settings', 'pages/settings.php');
post('/settings', 'pages/settings.php');

// Match request method and URL path
$method = $_SERVER['REQUEST_METHOD'];
$match = match_route($method, $uri);

if ($match) {
    $handler = $match['handler'];
    $routeParams = $match['params']; // passed to included file
    
    if (is_callable($handler)) {
        $handler($routeParams);
    } else {
        // Authenticate all routes except /login and /logout
        if ($uri !== '/login' && $uri !== '/logout') {
            check_auth();
        }
        
        if ($uri === '/login') {
            include $handler;
        } else {
            $page_content_file = $handler;
            include 'layout.php';
        }
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
