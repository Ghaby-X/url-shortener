<?php
class UrlController {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Generate a random short URL
    private function generateShortUrl() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 6;
        $shortUrl = '';
        
        for ($i = 0; $i < $length; $i++) {
            $shortUrl .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        // Check if short URL already exists
        $stmt = $this->conn->prepare("SELECT id FROM urls WHERE short_url = ?");
        $stmt->bind_param("s", $shortUrl);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // If exists, generate a new one recursively
            return $this->generateShortUrl();
        }
        
        return $shortUrl;
    }
    
    // Create a new short URL
    public function createShortUrl($email, $longUrl) {
        $shortUrl = $this->generateShortUrl();
        
        $stmt = $this->conn->prepare("INSERT INTO urls (email, short_url, long_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $shortUrl, $longUrl);
        
        if ($stmt->execute()) {
            return $shortUrl;
        } else {
            return false;
        }
    }
    
    // Get URLs by email
    public function getUrlsByEmail($email) {
        $stmt = $this->conn->prepare("SELECT short_url, long_url, visits FROM urls WHERE email = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $urls = [];
        while ($row = $result->fetch_assoc()) {
            $urls[] = $row;
        }
        
        return $urls;
    }
    
    // Redirect to original URL
    public function redirect($shortUrl) {
        $stmt = $this->conn->prepare("SELECT long_url FROM urls WHERE short_url = ?");
        $stmt->bind_param("s", $shortUrl);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $longUrl = $row['long_url'];
            
            // Update visit count
            $updateStmt = $this->conn->prepare("UPDATE urls SET visits = visits + 1 WHERE short_url = ?");
            $updateStmt->bind_param("s", $shortUrl);
            $updateStmt->execute();
            
            // Redirect to the original URL
            header("Location: " . $longUrl);
            exit;
        } else {
            // Short URL not found
            header("HTTP/1.0 404 Not Found");
            include '../templates/404.php';
            exit;
        }
    }
}