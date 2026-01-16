<?php
// views/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harum Bakery - <?php echo $pageTitle ?? 'Home'; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/admin/admin-style.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-harum sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-cupcake"></i> Harum Bakery
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard">
                            <i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/product"><i class="fas fa-heart"></i> Products</a>
                    </li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/about">
                            <i class="fas fa-heart me-1"></i> About
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- Search -->
                    <form class="d-flex me-3" action="<?php echo BASE_URL; ?>/modules/product" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search cakes..." aria-label="Search">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Cart -->
                    <a href="<?php echo BASE_URL; ?>/cart" class="btn btn-outline-primary me-3 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-counter" class="cart-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php
                            // Include class Cart dan cek jumlah item
                            if (isset($_SESSION['user_id'])) {
                                require_once __DIR__ . '/../class/Database.php';
                                require_once __DIR__ . '/../class/Cart.php';
                                $cart = new Cart();
                                $cartCount = $cart->getCartCount($_SESSION['user_id']);
                                echo $cartCount;
                            } else {
                                echo '0';
                            }
                            ?>
                        </span>
                    </a>
                    
                    <!-- User Menu -->
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                                <div class="me-2">
                                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <small>Hello,</small><br>
                                    <strong><?php echo $_SESSION['full_name']; ?></strong>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>/profile">
                                    <i class="fas fa-user me-2"></i> My Profile
                                </a>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>/orders">
                                    <i class="fas fa-history me-2"></i> My Orders
                                </a>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>/favorites">
                                    <i class="fas fa-heart me-2"></i> Favorites
                                </a>
                                <?php if (isAdmin()): ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/modules/admin">
                                        <i class="fas fa-cog me-2"></i> Admin Panel
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/modules/auth/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/login" class="btn btn-outline-primary me-2">Login</a>
                        <a href="<?php echo BASE_URL; ?>/register" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>