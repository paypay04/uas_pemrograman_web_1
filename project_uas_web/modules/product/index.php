<?php
require_once __DIR__ . '/../../config.php';

// Cek login untuk admin
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false && !isAdmin()) {
    redirect('/login');
}

// Include classes
require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Product.php';
require_once __DIR__ . '/../../class/Category.php';

// Inisialisasi
$product = new Product();
$category = new Category();

// Get parameters
$search = $_GET['search'] ?? '';
$categorySlug = $_GET['category'] ?? '';

// ========== PERBAIKAN DISINI ==========
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page < 1) {
    $page = 1;
}

$limit = 8;
$offset = ($page - 1) * $limit; // Sekarang $offset tidak akan negatif
// ========== AKHIR PERBAIKAN ==========

// Get data
if ($categorySlug) {
    $cat = $category->getBySlug($categorySlug);
    $categoryId = $cat ? $cat['id'] : null;
} else {
    $categoryId = null;
}

$products = $product->getAll($limit, $offset, $categoryId, $search);
$totalProducts = $product->countProducts($categoryId, $search);
$totalPages = ceil($totalProducts / $limit);

// Get all categories for filter
$categories = $category->getAll();

// Set page title
$pageTitle = 'Products';
if ($categorySlug && isset($cat['name'])) {
    $pageTitle = $cat['name'] . ' - Harum Bakery';
}

include __DIR__ . '/../../views/header.php';
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold text-dark">
                <i class="fas fa-bread-slice me-2 text-primary"></i>Our Products
            </h1>
            <p class="text-muted">Discover our delicious selection of freshly baked goods</p>
        </div>
        
        <!-- Tombol Add Product (hanya untuk admin) -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="<?php echo BASE_URL; ?>/product/tambah" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
        <?php endif; ?>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" action="" class="search-form">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search cakes, bread, cookies..." 
                            value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="dropdown">
                <button class="btn btn-outline-primary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-2"></i>
                    <?php echo $categorySlug ? $cat['name'] : 'All Categories'; ?>
                </button>
                <ul class="dropdown-menu w-100">
                    <li><a class="dropdown-item" href="?page=products">All Categories</a></li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a class="dropdown-item" href="?page=products&category=<?php echo $cat['slug']; ?>">
                            <i class="<?php echo $cat['icon']; ?> me-2"></i>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    <?php if (!empty($products)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 mb-5">
        <?php foreach ($products as $prod): ?>
        <div class="col">
            <div class="card product-card h-100">
                <!-- Product Image -->
                <div class="position-relative">
                    <img src="<?php echo BASE_URL; ?>/assets/gambar/<?php echo $prod['image_url'] ?? 'default.jpg'; ?>"
                            class="card-img-top product-image" 
                            alt="<?php echo htmlspecialchars($prod['name']); ?>"
                            style="height: 200px; object-fit: cover;">
                    
                    <!-- Discount Badge -->
                    <?php if ($prod['discount_price']): ?>
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                        -<?php echo round((($prod['price'] - $prod['discount_price']) / $prod['price']) * 100); ?>%
                    </span>
                    <?php endif; ?>
                    
                    <!-- Featured Badge -->
                    <?php if ($prod['is_featured']): ?>
                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                        <i class="fas fa-star me-1"></i>Featured
                    </span>
                    <?php endif; ?>
                    
                    <!-- Stock Status -->
                    <?php if ($prod['stock'] < 5): ?>
                    <span class="badge bg-danger position-absolute bottom-0 start-0 m-2">
                        Low Stock
                    </span>
                    <?php elseif ($prod['stock'] == 0): ?>
                    <span class="badge bg-secondary position-absolute bottom-0 start-0 m-2">
                        Out of Stock
                    </span>
                    <?php endif; ?>
                </div>
                
                <!-- Card Body -->
                <div class="card-body d-flex flex-column">
                    <!-- Category -->
                    <small class="text-muted mb-1">
                        <i class="<?php echo $prod['icon'] ?? 'fas fa-tag'; ?> me-1"></i>
                        <?php echo htmlspecialchars($prod['category_name'] ?? 'Uncategorized'); ?>
                    </small>
                    
                    <!-- Product Name -->
                    <h5 class="card-title text-dark mb-2">
                        <?php echo htmlspecialchars($prod['name']); ?>
                    </h5>
                    
                    <!-- Description -->
                    <p class="card-text text-muted small flex-grow-1">
                        <?php echo strlen($prod['description']) > 80 ? 
                                substr($prod['description'], 0, 80) . '...' : 
                                $prod['description']; ?>
                    </p>
                    
                    <!-- Price -->
                    <div class="mb-3">
                        <?php if ($prod['discount_price']): ?>
                        <div class="d-flex align-items-center">
                            <span class="h5 fw-bold text-primary mb-0">
                                Rp <?php echo number_format($prod['discount_price'], 0, ',', '.'); ?>
                            </span>
                            <span class="text-muted text-decoration-line-through ms-2">
                                Rp <?php echo number_format($prod['price'], 0, ',', '.'); ?>
                            </span>
                        </div>
                        <?php else: ?>
                        <span class="h5 fw-bold text-primary mb-0">
                            Rp <?php echo number_format($prod['price'], 0, ',', '.'); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <div class="stock-info">
                            <small class="<?php echo $prod['stock'] > 10 ? 'text-success' : 'text-danger'; ?>">
                                <i class="fas fa-box me-1"></i>
                                Stock: <?php echo $prod['stock']; ?>
                            </small>
                        </div>
                        
                        <div class="btn-group">
                            <!-- View Details -->
                            <a href="<?php echo BASE_URL; ?>/product_detail?id=<?php echo $prod['id']; ?>" 
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if ($prod['stock'] > 0): ?>
                                <button type="button" 
                                        class="btn btn-sm btn-primary add-cart-btn"
                                        data-id="<?php echo $prod['id']; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled>
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php endif; ?>
                            
                            <script>
                            // Event listener untuk semua tombol add to cart
                            document.addEventListener('DOMContentLoaded', function() {
                                document.querySelectorAll('.add-cart-btn').forEach(button => {
                                    button.addEventListener('click', function() {
                                        const productId = this.getAttribute('data-id');
                                        addToCartWithLoading(this, productId);
                                    });
                                });
                            });
                            
                            function addToCartWithLoading(button, productId) {
                                // Simpan state asli
                                const originalHTML = button.innerHTML;
                                const originalClass = button.className;
                                
                                // Set loading state
                                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                                button.className = button.className + ' disabled';
                                
                                // Gunakan path absolut
                                fetch('<?php echo BASE_URL; ?>/modules/cart/add.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: new URLSearchParams({
                                        'product_id': productId,
                                        'quantity': 1
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        button.innerHTML = '<i class="fas fa-check"></i>';
                                        button.className = button.className.replace('btn-primary', 'btn-success');
                                        
                                        // Update cart counter
                                        const cartCounter = document.getElementById('cart-counter');
                                        if (cartCounter) {
                                            cartCounter.textContent = data.cartCount;
                                            cartCounter.style.animation = 'none';
                                            setTimeout(() => {
                                                cartCounter.style.animation = 'pulse 0.5s';
                                            }, 10);
                                        }
                                        
                                        setTimeout(() => {
                                            button.innerHTML = originalHTML;
                                            button.className = originalClass.replace(' disabled', '');
                                        }, 1000);
                                        
                                    } else {
                                        button.innerHTML = '<i class="fas fa-times"></i>';
                                        button.className = button.className.replace('btn-primary', 'btn-danger');
                                        
                                        setTimeout(() => {
                                            button.innerHTML = originalHTML;
                                            button.className = originalClass.replace(' disabled', '');
                                        }, 2000);
                                        
                                        if (data.redirect) {
                                            setTimeout(() => {
                                                window.location.href = data.redirect;
                                            }, 1500);
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    button.innerHTML = originalHTML;
                                    button.className = originalClass.replace(' disabled', '');
                                });
                            }
                            </script>
                            
                            <style>
                            @keyframes pulse {
                                0% { transform: translate(-50%, -50%) scale(1); }
                                50% { transform: translate(-50%, -50%) scale(1.3); }
                                100% { transform: translate(-50%, -50%) scale(1); }
                            }
                            </style>
                            <?php if (isLoggedIn()): ?>
                            <form action="../modules/favorite/toggle.php" method="POST" class="d-inline">
                                <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger favorite-btn">
                                    <i class="far fa-heart"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Product pagination">
        <ul class="pagination justify-content-center">
            <!-- Previous -->
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=products&p=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
            
            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=products&p=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <!-- Next -->
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=products&p=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $categorySlug ? '&category=' . $categorySlug : ''; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

    <?php else: ?>
    <!-- No Products Found -->
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-4x text-muted"></i>
        </div>
        <h4 class="text-dark mb-3">No products found</h4>
        <p class="text-muted mb-4">Try adjusting your search or filter to find what you're looking for.</p>
        <a href="?page=products" class="btn btn-primary">
            <i class="fas fa-undo me-2"></i>View All Products
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.product-card {
    border: 1px solid #e9ecef;
    border-radius: 15px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(255, 182, 193, 0.2);
    border-color: #B99976;
}

.product-image {
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.favorite-btn:hover {
    background-color: #B99976;
    border-color: #B99976;
    color: white;
}

.search-form .form-control {
    border-radius: 25px 0 0 25px;
    border: 2px solid #B99976;
}

.search-form .btn {
    border-radius: 0 25px 25px 0;
}

.page-item.active .page-link {
    background-color: #B99976;
    border-color: #B99976;
}

.page-link {
    color: #895737;
}

.page-link:hover {
    color: #734128;
}
</style>

<?php include __DIR__ . '/../../views/footer.php'; ?>