<?php
// modules/product/product_detail.php
require_once __DIR__ . '/../../config.php';

// Cek apakah parameter ID ada
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/products');
    exit();
}

// Include classes
require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Product.php';
require_once __DIR__ . '/../../class/Category.php';
require_once __DIR__ . '/../../class/Cart.php';

$product = new Product();
$category = new Category();
$cart = new Cart();

$productId = (int)$_GET['id'];
$productData = $product->getById($productId);

// Jika produk tidak ditemukan
if (!$productData) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/../../views/404.php';
    exit();
}

// Cek apakah produk aktif
if (!$productData['is_active']) {
    $_SESSION['error'] = 'Produk tidak tersedia';
    redirect('/products');
    exit();
}

// Handle add to cart
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if (isset($_SESSION['user_id'])) {
        if ($cart->addItem($_SESSION['user_id'], $productId, $quantity)) {
            $message = 'Produk berhasil ditambahkan ke keranjang';
            $messageType = 'success';
        } else {
            $message = 'Gagal menambahkan ke keranjang';
            $messageType = 'danger';
        }
    } else {
        $_SESSION['redirect_to'] = 'product_detail&id=' . $productId;
        redirect('/login');
        exit();
    }
}

// Get related products (produk dalam kategori yang sama)
$relatedProducts = [];
if ($productData['category_id']) {
    // Anda perlu menambahkan method getByCategory di Product class
    // Atau gunakan getAll dengan filter category
    $relatedProducts = $product->getAll(4, 0, $productData['category_id'], '');
}

$pageTitle = $productData['name'] . ' - Harum Bakery';
include __DIR__ . '/../../views/header.php';
?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="container mt-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/product">Products</a></li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($productData['name']); ?></li>
    </ol>
</nav>

<div class="container py-5">
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="text-center">
                        <img src="<?php echo BASE_URL; ?>/assets/gambar/<?php echo $productData['image_url'] ?? 'default.jpg'; ?>" 
                                alt="<?php echo htmlspecialchars($productData['name']); ?>" 
                                class="img-fluid rounded" 
                                style="max-height: 400px; object-fit: contain;">
                    </div>
                    
                    <!-- Thumbnails (jika ada multiple images) -->
                    <div class="d-flex justify-content-center mt-3">
                        <div class="thumbnail me-2">
                            <img src="<?php echo BASE_URL; ?>/assets/gambar/<?php echo $productData['image_url'] ?? 'default.jpg'; ?>" 
                                    class="img-thumbnail active" 
                                    style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;">
                        </div>
                        <!-- Tambahkan thumbnails lain jika ada -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Product Name -->
                    <h1 class="h2 fw-bold text-dark mb-2"><?php echo htmlspecialchars($productData['name']); ?></h1>
                    
                    <!-- Category -->
                    <div class="mb-3">
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-tag me-1"></i>
                            <?php echo htmlspecialchars($productData['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                        <?php if ($productData['is_featured']): ?>
                        <span class="badge bg-warning ms-2">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        <?php if ($productData['discount_price']): ?>
                        <div class="d-flex align-items-center">
                            <h3 class="text-primary fw-bold me-3">
                                Rp <?php echo number_format($productData['discount_price'], 0, ',', '.'); ?>
                            </h3>
                            <h5 class="text-muted text-decoration-line-through">
                                Rp <?php echo number_format($productData['price'], 0, ',', '.'); ?>
                            </h5>
                            <span class="badge bg-danger ms-2">
                                -<?php echo round((($productData['price'] - $productData['discount_price']) / $productData['price']) * 100); ?>%
                            </span>
                        </div>
                        <?php else: ?>
                        <h3 class="text-primary fw-bold">
                            Rp <?php echo number_format($productData['price'], 0, ',', '.'); ?>
                        </h3>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="text-dark mb-2">Description</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($productData['description'] ?? 'No description available.')); ?></p>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="mb-4">
                        <h5 class="text-dark mb-2">Availability</h5>
                        <?php if ($productData['stock'] > 10): ?>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>In Stock (<?php echo $productData['stock']; ?> items)
                        </span>
                        <?php elseif ($productData['stock'] > 0 && $productData['stock'] <= 10): ?>
                        <span class="badge bg-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Low Stock (<?php echo $productData['stock']; ?> left)
                        </span>
                        <?php else: ?>
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Add to Cart Form -->
                    <form method="POST" action="" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label text-dark fw-bold">Quantity</label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                                    <input type="number" name="quantity" id="quantity" 
                                           value="1" min="1" max="<?php echo min($productData['stock'], 10); ?>"
                                           class="form-control text-center">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>
                            <div class="col-auto">
                                <input type="hidden" name="add_to_cart" value="1">
                                <button type="submit" class="btn btn-primary btn-lg px-4" 
                                        <?php echo $productData['stock'] == 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    <?php echo $productData['stock'] == 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                </button>

                                <!-- Di product_detail.php, di bagian action buttons -->
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="<?php echo BASE_URL; ?>/product/edit?id=<?php echo $productData['id']; ?>" 
                                   class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Additional Info -->
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">
                                    <i class="fas fa-shipping-fast me-1"></i> Free shipping over Rp 200,000
                                </small>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block">
                                    <i class="fas fa-undo me-1"></i> 7-day return policy
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="mt-5">
        <h3 class="h4 fw-bold text-dark mb-4">Related Products</h3>
        <div class="row g-4">
            <?php foreach ($relatedProducts as $related): ?>
                <?php if ($related['id'] != $productId): ?>
                <div class="col-md-3">
                    <div class="card product-card border-0 shadow-sm h-100">
                        <a href="?page=product_detail&id=<?php echo $related['id']; ?>" class="text-decoration-none">
                            <div class="position-relative">
                                <img src="<?php echo BASE_URL; ?>/assets/gambar/<?php echo $related['image_url'] ?? 'default.jpg'; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($related['name']); ?>"
                                     style="height: 180px; object-fit: cover;">
                                <?php if ($related['discount_price']): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                    -<?php echo round((($related['price'] - $related['discount_price']) / $related['price']) * 100); ?>%
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title text-dark"><?php echo htmlspecialchars($related['name']); ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($related['discount_price']): ?>
                                        <div class="text-primary fw-bold">
                                            Rp <?php echo number_format($related['discount_price'], 0, ',', '.'); ?>
                                        </div>
                                        <small class="text-muted text-decoration-line-through">
                                            Rp <?php echo number_format($related['price'], 0, ',', '.'); ?>
                                        </small>
                                        <?php else: ?>
                                        <div class="text-primary fw-bold">
                                            Rp <?php echo number_format($related['price'], 0, ',', '.'); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo htmlspecialchars($related['category_name'] ?? ''); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function changeQuantity(change) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) + change;
    const max = parseInt(input.max);
    const min = parseInt(input.min);
    
    if (value > max) value = max;
    if (value < min) value = min;
    
    input.value = value;
}

// Thumbnail click event
document.querySelectorAll('.thumbnail img').forEach(img => {
    img.addEventListener('click', function() {
        const mainImage = document.querySelector('.img-fluid');
        mainImage.src = this.src;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail img').forEach(thumb => {
            thumb.classList.remove('active');
            thumb.style.borderColor = '#dee2e6';
        });
        
        // Add active class to clicked thumbnail
        this.classList.add('active');
        this.style.borderColor = '#B99976';
    });
});
</script>

<style>
.product-card {
    transition: transform 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.img-thumbnail.active {
    border-color: #B99976 !important;
    border-width: 2px;
}
.breadcrumb {
    background-color: transparent;
    padding: 0;
}
</style>

<?php include __DIR__ . '/../../views/footer.php'; ?>