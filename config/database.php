<?php
// Load environment variables from .env file
function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load environment variables
loadEnv();

// Database configuration from .env
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'url_shortener';

// Create database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . $dbName;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbName);

// Create urls table if not exists
$sql = "CREATE TABLE IF NOT EXISTS urls (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    short_url VARCHAR(10) NOT NULL UNIQUE,
    long_url TEXT NOT NULL,
    visits INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}
?>