<?php
require_once __DIR__ . '/../../config.php';

// Cek login
if (!isLoggedIn()) {
    $_SESSION['redirect_to'] = 'cart';
    redirect('/login');
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Cart.php';
require_once __DIR__ . '/../../class/Product.php';

$cart = new Cart();
$product = new Product();

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    switch ($action) {
        case 'update':
            if ($cart->updateQuantity($_SESSION['user_id'], $productId, $quantity)) {
                $message = 'Cart updated successfully';
                $messageType = 'success';
            } else {
                $message = 'Failed to update cart';
                $messageType = 'danger';
            }
            break;
            
        case 'remove':
            if ($cart->removeItem($_SESSION['user_id'], $productId)) {
                $message = 'Item removed from cart';
                $messageType = 'success';
            } else {
                $message = 'Failed to remove item';
                $messageType = 'danger';
            }
            break;
            
        case 'clear':
            if ($cart->clearCart($_SESSION['user_id'])) {
                $message = 'Cart cleared successfully';
                $messageType = 'success';
            } else {
                $message = 'Failed to clear cart';
                $messageType = 'danger';
            }
            break;
    }
}

// Get cart items
$cartItems = $cart->getCartItems($_SESSION['user_id']);
$totalItems = count($cartItems);
$subtotal = 0;
$shipping = 15000; // Flat rate shipping
$taxRate = 0.11; // 11% tax

// Calculate totals
foreach ($cartItems as $item) {
    $price = $item['discount_price'] ?: $item['price'];
    $subtotal += $price * $item['quantity'];
}

$tax = $subtotal * $taxRate;
$total = $subtotal + $tax + $shipping;

$pageTitle = 'Shopping Cart';
include __DIR__ . '/../../views/header.php';
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 fw-bold text-dark mb-3">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>Your Shopping Cart
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item active">Cart</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8 mb-4">
            <?php if ($totalItems > 0): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-box me-2"></i><?php echo $totalItems; ?> Item(s) in Cart
                        </h5>
                        <form method="POST" action="" onsubmit="return confirm('Clear all items from cart?')">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;"></th>
                                    <th>Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                <tr class="cart-item-row">
                                    <!-- Product Image -->
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>/assets/gambar/<?php echo $item['image_url'] ?? 'default.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    
                                    <!-- Product Info -->
                                    <td>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($item['category_name']); ?>
                                        </small>
                                        <div>
                                            <small class="<?php echo $item['stock'] > 5 ? 'text-success' : 'text-danger'; ?>">
                                                <i class="fas fa-box me-1"></i>
                                                <?php echo $item['stock']; ?> in stock
                                            </small>
                                        </div>
                                    </td>
                                    
                                    <!-- Price -->
                                    <td class="text-center">
                                        <?php if ($item['discount_price']): ?>
                                        <div>
                                            <span class="fw-bold text-primary">
                                                Rp <?php echo number_format($item['discount_price'], 0, ',', '.'); ?>
                                            </span>
                                            <div>
                                                <small class="text-muted text-decoration-line-through">
                                                    Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <span class="fw-bold text-primary">
                                            Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Quantity -->
                                    <td class="text-center">
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" name="quantity" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="<?php echo min($item['stock'], 10); ?>"
                                                       class="form-control text-center" 
                                                       onchange="this.form.submit()">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    
                                    <!-- Total -->
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">
                                            Rp <?php echo number_format(($item['discount_price'] ?: $item['price']) * $item['quantity'], 0, ',', '.'); ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Remove -->
                                    <td class="text-center">
                                        <form method="POST" action="" onsubmit="return confirm('Remove this item from cart?')">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Empty Cart -->
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-dark mb-3">Your cart is empty</h4>
                    <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                    <a href="?page=products" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                </div>
                
                <div class="card-body">
                    <!-- Summary Details -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping</span>
                            <span>Rp <?php echo number_format($shipping, 0, ',', '.'); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tax (11%)</span>
                            <span>Rp <?php echo number_format($tax, 0, ',', '.'); ?></span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5 text-dark">Total</span>
                            <span class="h5 text-primary fw-bold">
                                Rp <?php echo number_format($total, 0, ',', '.'); ?>
                            </span>
                        </div>
                        
                        <!-- Promotion Code -->
                        <div class="mb-4">
                            <label class="form-label text-muted">Promotion Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter code">
                                <button class="btn btn-outline-primary" type="button">Apply</button>
                            </div>
                        </div>
                        
                        <!-- Checkout Button -->
                        <?php if ($totalItems > 0): ?>
                        <div class="d-grid gap-2">
                            <a href="?page=checkout" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                            <a href="?page=products" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Security Info -->
                    <div class="alert alert-light border mt-3">
                        <div class="d-flex">
                            <i class="fas fa-shield-alt text-primary fa-lg me-3"></i>
                            <div>
                                <h6 class="mb-1">Secure Checkout</h6>
                                <small class="text-muted">
                                    Your payment information is encrypted and secure.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateQuantity(productId, change) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const action = document.createElement('input');
    action.type = 'hidden';
    action.name = 'action';
    action.value = 'update';
    form.appendChild(action);
    
    const product = document.createElement('input');
    product.type = 'hidden';
    product.name = 'product_id';
    product.value = productId;
    form.appendChild(product);
    
    // Find current quantity
    const row = document.querySelector(`[data-product-id="${productId}"]`);
    let currentQty = 1;
    if (row) {
        const qtyInput = row.querySelector('input[name="quantity"]');
        if (qtyInput) {
            currentQty = parseInt(qtyInput.value);
        }
    }
    
    const quantity = document.createElement('input');
    quantity.type = 'hidden';
    quantity.name = 'quantity';
    quantity.value = Math.max(1, currentQty + change);
    form.appendChild(quantity);
    
    document.body.appendChild(form);
    form.submit();
}

// Add product-id attribute to rows
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.cart-item-row');
    rows.forEach((row, index) => {
        const productId = row.querySelector('input[name="product_id"]')?.value;
        if (productId) {
            row.setAttribute('data-product-id', productId);
        }
    });
});
</script>

<style>
.cart-item-row:hover {
    background-color: rgba(255, 182, 193, 0.05);
}

.input-group .btn-outline-secondary:hover {
    background-color: #B99976;
    border-color: #B99976;
    color: white;
}

.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #4A4A4A;
}

.alert-light {
    background-color: #FFF0F5;
    border-color: #B99976;
}
</style>

<?php include __DIR__ . '/../../views/footer.php'; ?>