<?php
// API endpoint to get user's URLs
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../src/controllers/UrlController.php';

// Get email from query parameter
$email = $_GET['email'] ?? '';

if (empty($email)) {
    echo json_encode(['error' => 'Email is required']);
    exit;
}

$controller = new UrlController($conn);
$urls = $controller->getUrlsByEmail($email);

echo json_encode($urls);
?>