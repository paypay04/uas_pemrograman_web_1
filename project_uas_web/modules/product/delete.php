<?php
require_once __DIR__ . '/../../config.php';

// Cek admin access
if (!isLoggedIn() || !isAdmin()) {
    redirect('/login');
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Product.php';

$product = new Product();

// Get product ID
$id = $_GET['id'] ?? 0;

if ($id) {
    // Get product data first (for image deletion)
    $productData = $product->getById($id);
    
    // Delete product
    if ($product->delete($id)) {
        // Delete image file if exists
        if ($productData && $productData['image_url']) {
            $imagePath = __DIR__ . '/../../assets/gambar/products/' . $productData['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $_SESSION['success'] = 'Product deleted successfully!';
    } else {
        $_SESSION['error'] = 'Failed to delete product';
    }
}

// Redirect back to products page
redirect('?page=products');
?>