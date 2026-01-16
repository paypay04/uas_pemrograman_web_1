<?php
// modules/product/delete.php
require_once __DIR__ . '/../../config.php';

// Cek login dan role admin
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = 'Access denied.';
    redirect('/login');
    exit();
}

// Cek parameter ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['error'] = 'Product ID is required';
    redirect('/product');
    exit();
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Product.php';

$productId = (int)$_POST['id'];
$product = new Product();
$productData = $product->getById($productId);

if ($productData) {
    // Delete image file if not default
    if ($productData['image_url'] != 'default.jpg' && $productData['image_url'] != '') {
        $imagePath = __DIR__ . '/../../assets/gambar/' . $productData['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Delete from database
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $product->db->prepare($sql);
    $stmt->bindParam(':id', $productId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Product deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete product';
    }
} else {
    $_SESSION['error'] = 'Product not found';
}

redirect('/product');
?>