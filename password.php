<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Admin Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Generate Admin Password Hash</h2>
                        
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
                            $password = $_POST['password'];
                            $hash = password_hash($password, PASSWORD_DEFAULT);
                            
                            echo '<div class="alert alert-success">';
                            echo '<h5>Password Hash Generated!</h5>';
                            echo '<p class="mb-2"><strong>Your Password:</strong> ' . htmlspecialchars($password) . '</p>';
                            echo '<p class="mb-0"><strong>Hash to use in index.php:</strong></p>';
                            echo '<code style="word-break: break-all;">' . htmlspecialchars($hash) . '</code>';
                            echo '<hr>';
                            echo '<p class="mb-0 small">Copy the hash above and replace the ADMIN_PASS value in index.php line 13</p>';
                            echo '</div>';
                        }
                        ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="password" class="form-label">Enter Your Desired Admin Password</label>
                                <input type="text" class="form-control" id="password" name="password" required 
                                       placeholder="e.g., MySecurePassword123!">
                                <div class="form-text">Use a strong password with letters, numbers, and special characters</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Generate Password Hash
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="alert alert-info">
                            <h6>Instructions:</h6>
                            <ol class="mb-0 small">
                                <li>Enter your desired password above</li>
                                <li>Click "Generate Password Hash"</li>
                                <li>Copy the generated hash</li>
                                <li>Open <code>index.php</code> and find line 13</li>
                                <li>Replace <code>$2y$10$YourHashedPasswordHere</code> with your generated hash</li>
                                <li>Delete this file (<code>generate_password.php</code>) after use for security</li>
                            </ol>
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>Security Note:</strong> The default admin username is <code>admin</code>. 
                            You can change this in index.php line 12 (ADMIN_USER constant).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>