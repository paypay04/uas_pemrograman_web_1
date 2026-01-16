<?php
require_once __DIR__ . '/../../config.php';

// Cek login
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please login first to add items to cart',
        'redirect' => BASE_URL . '/login'
    ]);
    exit();
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Cart.php';
require_once __DIR__ . '/../../class/Product.php';

$cart = new Cart();
$product = new Product();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    // Validasi produk
    $productData = $product->getById($productId);
    if (!$productData) {
        echo json_encode([
            'success' => false, 
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    // Cek apakah produk aktif
    if (!$productData['is_active']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Product is not available'
        ]);
        exit();
    }
    
    // Validasi stock
    if ($productData['stock'] < $quantity) {
        echo json_encode([
            'success' => false, 
            'message' => 'Only ' . $productData['stock'] . ' items left in stock'
        ]);
        exit();
    }
    
    if ($productData['stock'] == 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Product is out of stock'
        ]);
        exit();
    }
    
    // Tambah ke keranjang
    $result = $cart->addItem($_SESSION['user_id'], $productId, $quantity);
    
    // Di add.php, setelah berhasil menambah ke cart
    // Di add.php - pastikan format response benar
    if ($result) {
        $cartCount = $cart->getCartCount($_SESSION['user_id']);
        
        echo json_encode([
            'success' => true,
            'cartCount' => $cartCount,
            'productName' => $productData['name'],
            'quantity' => $quantity,
            'price' => $productData['discount_price'] ?: $productData['price']
        ]);
        exit();
    }
}
?>