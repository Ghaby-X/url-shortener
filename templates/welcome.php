<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center">Welcome to URL Shortener</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Create short, memorable links in seconds!</h5>
                        
                        <form id="emailForm" class="mb-3">
                            <div class="mb-3">
                                <label for="email" class="form-label">Enter your email to get started</label>
                                <input type="email" class="form-control" id="email" required placeholder="your@email.com">
                                <div class="form-text">We'll use this to keep track of your shortened URLs.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Get Started</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('emailForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            
            // Store email in localStorage
            localStorage.setItem('userEmail', email);
            
            // Redirect to URLs page
            window.location.href = '/urls';
        });
    </script>
</body>
</html>