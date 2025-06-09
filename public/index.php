<?php
// Main entry point
session_start();
require_once '../config/database.php';
require_once '../src/controllers/UrlController.php';

// Simple routing
$path = $_SERVER['REQUEST_URI'] ?? '/';
$path = trim(parse_url($path, PHP_URL_PATH), '/');
$path = explode('/', $path);
$route = end($path);

// Route handling
if ($route === '' || $route === 'index.php') {
    // Welcome page
    include '../templates/welcome.php';
} elseif ($route === 'urls') {
    // URLs page
    include '../templates/urls.php';
} elseif (preg_match('/^[a-zA-Z0-9]{6}$/', $route)) {
    // Redirect short URL
    $controller = new UrlController($conn);
    $controller->redirect($route);
} else {
    // 404 Not Found
    header("HTTP/1.0 404 Not Found");
    include '../templates/404.php';
}
?>