<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4"><?php echo __('login.title'); ?></h2>

                    <form method="POST" action="index.php">
                        <input type="hidden" name="action" value="login">

                        <div class="mb-3">
                            <label for="username" class="form-label"><?php echo __('login.user'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><?php echo __('login.pass'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> <?php echo __('login.btn'); ?>
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <p class="text-center text-muted small mb-0"><?php echo __('login.note'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>