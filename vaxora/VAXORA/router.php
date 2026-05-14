<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Map clean paths to PHP files
$routes = [
    '/'                   => 'index.php',
    '/index.php'          => 'index.php',
    '/dashboard.php'      => 'dashboard.php',
    '/children.php'       => 'children.php',
    '/add_child.php'      => 'add_child.php',
    '/edit_child.php'     => 'edit_child.php',
    '/book_appointment.php' => 'book_appointment.php',
    '/vaccination_history.php' => 'vaccination_history.php',
    '/contact.php'        => 'contact.php',
];

if (isset($routes[$uri])) {
    require __DIR__ . '/' . $routes[$uri];
    return;
}

// Auto-route sub-paths
$file = __DIR__ . $uri;
if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    return;
}

if (file_exists($file . '.php')) {
    require $file . '.php';
    return;
}

// 404
http_response_code(404);
echo '<h1>404 — Page Not Found</h1><a href="/">← Home</a>';
