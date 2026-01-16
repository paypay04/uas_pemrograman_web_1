<?php
// index.php
session_start();

// Include config
require_once __DIR__ . '/config.php';

// Simple routing
$request = $_SERVER['REQUEST_URI'] ?? '/';
$base_path = '/project_uas_web';

// Remove base path dan query string
$request = str_replace($base_path, '', $request);
$request = strtok($request, '?');

// Debug: Tampilkan request untuk troubleshooting
// echo "Request: $request<br>";

// Routing
switch ($request) {
    case '/':
    case '':
        // Home page
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            // Jika sudah login, redirect ke dashboard
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        } else {
            // Tampilkan homepage untuk guest
            require_once __DIR__ . '/views/header.php';
            echo '<div class="container mt-5">
                    <h1 class="text-center">Welcome to Harum Bakery</h1>
                    <p class="text-center">Please login to access the dashboard</p>
                    <div class="text-center">
                        <a href="' . BASE_URL . '/modules/auth/login" class="btn btn-primary">Login</a>
                    </div>
                  </div>';
            require_once __DIR__ . '/views/footer.php';
        }
        break;
        
    case '/login':
        require_once __DIR__ . '/modules/auth/login.php';
        break;
        
    case '/dashboard':
        // Redirect langsung ke views/dashboard.php
        require_once __DIR__ . '/views/dashboard.php';
        break;
    
    case '/about':
    case '/modules/about':
        require_once __DIR__ . '/modules/about/index.php';
        break;

    case '/product':
        require_once __DIR__ . '/modules/product/index.php';
        break;

    case '/product/tambah':
        require_once __DIR__ . '/modules/product/tambah.php';
        break;

    // di index.php utama (root folder)
    case '/product_detail':
        require_once __DIR__ . '/modules/product/product_detail.php';
        break;
        
    case '/cart':
        require_once __DIR__ . '/modules/cart/index.php';
        break;

    case '/product/edit':
        require_once __DIR__ . '/modules/product/edit.php';
        break;
        
    case '/product/tambah':
        require_once __DIR__ . '/modules/product/tambah.php';
        break;
    
    default:
        // Coba include file jika ada
        $filePath = __DIR__ . $request . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            // 404 Not Found
            http_response_code(404);
            echo '404 - Page not found: ' . htmlspecialchars($request);
        }
        break;
}
?>