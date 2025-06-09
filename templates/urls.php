<?php
require_once '../src/controllers/UrlController.php';
$controller = new UrlController($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['long_url'])) {
    $email = $_POST['email'] ?? '';
    $longUrl = $_POST['long_url'] ?? '';
    
    if (!empty($email) && !empty($longUrl)) {
        $controller->createShortUrl($email, $longUrl);
    }
}

// Get user's URLs
$userEmail = '';
$urls = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Short URLs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Your Short URLs</h2>
        
        <!-- Create new URL form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Create New Short URL</h5>
            </div>
            <div class="card-body">
                <form id="urlForm" method="POST">
                    <input type="hidden" id="emailInput" name="email">
                    <div class="mb-3">
                        <label for="long_url" class="form-label">Enter a long URL to shorten</label>
                        <input type="url" class="form-control" id="long_url" name="long_url" required placeholder="https://example.com/very/long/url/that/needs/shortening">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Short URL</button>
                </form>
            </div>
        </div>
        
        <!-- URLs table -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Your URLs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="urlsTable">
                        <thead>
                            <tr>
                                <th>Short URL</th>
                                <th>Original URL</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody id="urlsTableBody">
                            <!-- Table rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div id="noUrls" class="text-center py-3 d-none">
                    <p class="text-muted">You haven't created any short URLs yet.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get email from localStorage
            const userEmail = localStorage.getItem('userEmail');
            
            // If no email, redirect back to welcome page
            if (!userEmail) {
                window.location.href = '/';
                return;
            }
            
            // Set email in hidden form field
            document.getElementById('emailInput').value = userEmail;
            
            // Fetch user's URLs
            fetchUserUrls(userEmail);
        });
        
        function fetchUserUrls(email) {
            fetch(`/api/urls.php?email=${encodeURIComponent(email)}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('urlsTableBody');
                    const noUrls = document.getElementById('noUrls');
                    
                    if (data.length > 0) {
                        tableBody.innerHTML = '';
                        data.forEach(url => {
                            tableBody.innerHTML += `
                                <tr>
                                    <td><a href="/${url.short_url}" target="_blank">${window.location.origin}/${url.short_url}</a></td>
                                    <td><a href="${url.long_url}" target="_blank">${truncateUrl(url.long_url)}</a></td>
                                    <td>${url.visits}</td>
                                </tr>
                            `;
                        });
                        noUrls.classList.add('d-none');
                    } else {
                        tableBody.innerHTML = '';
                        noUrls.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error fetching URLs:', error);
                });
        }
        
        function truncateUrl(url) {
            return url.length > 50 ? url.substring(0, 47) + '...' : url;
        }
    </script>
</body>
</html>