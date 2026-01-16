<?php
require_once __DIR__ . '/../../config.php';

// Cek login
if (!isLoggedIn()) {
    $_SESSION['redirect_to'] = 'checkout';
    redirect('/login');
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Cart.php';
require_once __DIR__ . '/../../class/Product.php';
require_once __DIR__ . '/../../class/Order.php';
require_once __DIR__ . '/../../class/User.php';

$cart = new Cart();
$product = new Product();
$order = new Order();
$user = new User();

// Get cart items
$cartItems = $cart->getCartItems($_SESSION['user_id']);
$totalItems = count($cartItems);

if ($totalItems === 0) {
    redirect('?page=cart');
}

// Get user data
$userData = $user->getById($_SESSION['user_id']);

// Calculate totals
$subtotal = 0;
$shipping = 15000;
$taxRate = 0.11;

foreach ($cartItems as $item) {
    $price = $item['discount_price'] ?: $item['price'];
    $subtotal += $price * $item['quantity'];
}

$tax = $subtotal * $taxRate;
$total = $subtotal + $tax + $shipping;

$message = '';
$messageType = '';

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate stock
    $stockValid = true;
    $outOfStockItems = [];
    
    foreach ($cartItems as $item) {
        if ($item['stock'] < $item['quantity']) {
            $stockValid = false;
            $outOfStockItems[] = $item['name'];
        }
    }
    
    if (!$stockValid) {
        $message = 'Some items are out of stock: ' . implode(', ', $outOfStockItems);
        $messageType = 'danger';
    } else {
        // Create order
        $orderData = [
            'user_id' => $_SESSION['user_id'],
            'total_amount' => $total,
            'shipping_address' => $_POST['shipping_address'] ?? $userData['address'],
            'payment_method' => $_POST['payment_method'] ?? 'bank_transfer',
            'notes' => $_POST['notes'] ?? ''
        ];
        
        $orderId = $order->create($orderData, $cartItems);
        
        if ($orderId) {
            // Clear cart
            $cart->clearCart($_SESSION['user_id']);
            
            // Redirect to order confirmation
            $_SESSION['order_success'] = true;
            $_SESSION['order_number'] = $orderId;
            redirect('?page=order_confirmation&id=' . $orderId);
        } else {
            $message = 'Failed to create order. Please try again.';
            $messageType = 'danger';
        }
    }
}

$pageTitle = 'Checkout';
include __DIR__ . '/../../views/header.php';
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 fw-bold text-dark mb-3">
                <i class="fas fa-credit-card me-2 text-primary"></i>Checkout
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="?page=cart">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
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

    <form method="POST" action="" id="checkoutForm">
        <div class="row">
            <!-- Left Column: Shipping & Payment -->
            <div class="col-lg-8">
                <!-- Shipping Address -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-shipping-fast me-2"></i>Shipping Address
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo htmlspecialchars($userData['full_name']); ?>" 
                                       required readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" 
                                       value="<?php echo htmlspecialchars($userData['phone']); ?>" 
                                       required readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Shipping Address *</label>
                                <textarea class="form-control" name="shipping_address" 
                                          rows="3" required><?php echo htmlspecialchars($userData['address']); ?></textarea>
                                <small class="text-muted">Please enter your complete shipping address</small>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="saveAddress">
                                    <label class="form-check-label" for="saveAddress">
                                        Save this address for future orders
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-credit-card me-2"></i>Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="bankTransfer" value="bank_transfer" checked>
                                    <label class="form-check-label" for="bankTransfer">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-university fa-2x me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">Bank Transfer</h6>
                                                <small class="text-muted">BCA, Mandiri, BRI, BNI</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="creditCard" value="credit_card">
                                    <label class="form-check-label" for="creditCard">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-credit-card fa-2x me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">Credit Card</h6>
                                                <small class="text-muted">Visa, Mastercard, JCB</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="cod" value="cash_on_delivery">
                                    <label class="form-check-label" for="cod">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-money-bill-wave fa-2x me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">Cash on Delivery</h6>
                                                <small class="text-muted">Pay when received</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check payment-method">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="ewallet" value="e_wallet">
                                    <label class="form-check-label" for="ewallet">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-mobile-alt fa-2x me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">E-Wallet</h6>
                                                <small class="text-muted">OVO, GoPay, DANA</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-sticky-note me-2"></i>Order Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" 
                                  placeholder="Any special instructions for your order? (optional)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <!-- Order Items -->
                        <div class="mb-4">
                            <h6 class="mb-3">Order Items (<?php echo $totalItems; ?>)</h6>
                            <div class="order-items">
                                <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="../assets/gambar/products/<?php echo $item['image_url'] ?? 'default.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <small class="d-block fw-bold"><?php echo htmlspecialchars($item['name']); ?></small>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                        </div>
                                    </div>
                                    <small class="fw-bold">
                                        Rp <?php echo number_format(($item['discount_price'] ?: $item['price']) * $item['quantity'], 0, ',', '.'); ?>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Price Summary -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
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
                        </div>
                        
                        <!-- Terms & Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms & Conditions</a>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i>Complete Order
                            </button>
                            <a href="?page=cart" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cart
                            </a>
                        </div>
                        
                        <!-- Security Info -->
                        <div class="alert alert-light border mt-3">
                            <div class="d-flex">
                                <i class="fas fa-shield-alt text-primary fa-lg me-3"></i>
                                <div>
                                    <h6 class="mb-1">100% Secure Payment</h6>
                                    <small class="text-muted">
                                        Your information is protected by SSL encryption.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const terms = document.getElementById('terms');
    if (!terms.checked) {
        e.preventDefault();
        alert('Please agree to the Terms & Conditions');
        terms.focus();
    }
});

// Payment method selection
document.querySelectorAll('.payment-method').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.payment-method').forEach(other => {
            other.classList.remove('selected');
        });
        this.classList.add('selected');
    });
});
</script>

<style>
.payment-method {
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #B99976;
    background-color: rgba(255, 182, 193, 0.05);
}

.payment-method.selected {
    border-color: #B99976;
    background-color: rgba(255, 182, 193, 0.1);
}

.form-check-input:checked {
    background-color: #B99976;
    border-color: #B99976;
}

.order-items {
    max-height: 200px;
    overflow-y: auto;
    padding-right: 10px;
}

.order-items::-webkit-scrollbar {
    width: 5px;
}

.order-items::-webkit-scrollbar-thumb {
    background-color: #B99976;
    border-radius: 10px;
}

textarea.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

textarea.form-control:focus {
    border-color: #B99976;
    box-shadow: 0 0 0 0.2rem rgba(255, 182, 193, 0.25);
}
</style>

<?php include __DIR__ . '/../../views/footer.php'; ?>