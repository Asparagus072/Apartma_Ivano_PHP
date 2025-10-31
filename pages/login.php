<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Login</h2>
                    <form method="POST" action="index.php?page=login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <p class="text-center mb-0">
                        Don't have an account? <a href="index.php?page=register">Register here</a>
                    </p>
                    <p class="text-center mt-3 text-muted small">
                        Admin login: Username: ADMIN / Password: admin123
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>