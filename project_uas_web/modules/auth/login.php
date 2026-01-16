<?php
// modules/auth/login.php

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config dengan path yang benar
require_once __DIR__ . '/../../config.php';

// Debug
// echo "Current path: " . __DIR__ . "<br>";

// Cek jika sudah login
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ' . BASE_URL . '/dashboard');
    } else {
        header('Location: ' . BASE_URL . '/');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Autoload sudah ada di config.php
    try {
        $user = new User();
        
        if ($user->login($username, $password)) {
            $_SESSION['success'] = 'Login successful! Welcome back!';
            
            if (isAdmin()) {
                header('Location: ' . BASE_URL . '/dashboard');
            } else {
                header('Location: ' . BASE_URL . '/');
            }
            exit();
        } else {
            $error = 'Invalid username/email or password';
        }
    } catch (Exception $e) {
        $error = 'Login error: ' . $e->getMessage();
    }
}

$pageTitle = 'Login';

// Include header
$headerPath = realpath(__DIR__ . '/../../views/header.php');
if ($headerPath) {
    include $headerPath;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-cupcake fa-3x text-primary"></i>
                        </div>
                        <h2 class="fw-bold">Welcome Back!</h2>
                        <p class="text-muted">Sign in to your Harum Bakery account</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" required
                                       placeholder="Enter username or email">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required
                                       placeholder="Enter password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                        </button>
                        
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">Forgot password?</a>
                            <p class="mt-3">
                                Don't have an account? 
                                <a href="<?php echo BASE_URL; ?>/register" class="text-primary fw-bold">
                                    Sign up here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>

<?php 
// Include footer
$footerPath = realpath(__DIR__ . '/../../views/footer.php');
if ($footerPath && file_exists($footerPath)) {
    include $footerPath;
} else {
    // Fallback minimal footer
    echo '
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
}
?>