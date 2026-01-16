<?php
require_once __DIR__ . '/../../config.php';

// Cek admin access
if (!isLoggedIn() || !isAdmin()) {
    redirect('/login');
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Product.php';
require_once __DIR__ . '/../../class/Category.php';

$product = new Product();
$category = new Category();

$message = '';
$messageType = '';

// Get product ID
$id = $_GET['id'] ?? 0;
$productData = $product->getById($id);

if (!$productData) {
    redirect('?page=products');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'price' => $_POST['price'] ?? 0,
        'discount_price' => !empty($_POST['discount_price']) ? $_POST['discount_price'] : null,
        'category_id' => $_POST['category_id'] ?? null,
        'stock' => $_POST['stock'] ?? 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/gambar/products/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;
        
        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Check file type
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Delete old image if exists
                if ($productData['image_url'] && file_exists($uploadDir . $productData['image_url'])) {
                    unlink($uploadDir . $productData['image_url']);
                }
                $data['image_url'] = $fileName;
            }
        }
    }
    
    // Update product
    if ($product->update($data)) {
        $message = 'Product updated successfully!';
        $messageType = 'success';
        // Refresh product data
        $productData = $product->getById($id);
    } else {
        $message = 'Failed to update product';
        $messageType = 'danger';
    }
}

$categories = $category->getAll();
$pageTitle = 'Edit Product';
include __DIR__ . '/../../views/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 text-dark">
                            <i class="fas fa-edit me-2 text-primary"></i>Edit Product
                        </h3>
                        <div>
                            <a href="?page=products" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <a href="?page=product_delete&id=<?php echo $id; ?>" 
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this product?')">
                                <i class="fas fa-trash me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data" id="productForm">
                        <div class="row g-4">
                            <!-- Current Image -->
                            <div class="col-md-12 text-center mb-4">
                                <?php if ($productData['image_url']): ?>
                                <img src="../assets/gambar/products/<?php echo $productData['image_url']; ?>" 
                                     alt="<?php echo htmlspecialchars($productData['name']); ?>"
                                     class="img-thumbnail" style="max-height: 200px;">
                                <?php else: ?>
                                <div class="text-muted">
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <p>No image uploaded</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Name -->
                            <div class="col-md-12">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($productData['name']); ?>" required>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3"><?php echo htmlspecialchars($productData['description']); ?></textarea>
                            </div>
                            
                            <!-- Price & Discount -->
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price * (Rp)</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo $productData['price']; ?>" required min="0" step="500">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="discount_price" class="form-label">Discount Price (Rp)</label>
                                <input type="number" class="form-control" id="discount_price" name="discount_price" 
                                       value="<?php echo $productData['discount_price']; ?>" min="0" step="500">
                            </div>
                            
                            <!-- Category & Stock -->
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo $cat['id'] == $productData['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="stock" class="form-label">Stock *</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       value="<?php echo $productData['stock']; ?>" required min="0">
                            </div>
                            
                            <!-- New Image Upload -->
                            <div class="col-md-12">
                                <label for="image" class="form-label">Update Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                                <div class="mt-2" id="imagePreview"></div>
                            </div>
                            
                            <!-- Checkboxes -->
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                        <?php echo $productData['is_featured'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Product
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                        <?php echo $productData['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active Product
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="col-md-12">
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-save me-2"></i>Update Product
                                    </button>
                                    <a href="?page=products" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.className = 'img-thumbnail mt-2';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(this.files[0]);
    }
});

// Form validation
document.getElementById('productForm').addEventListener('submit', function(e) {
    const price = parseFloat(document.getElementById('price').value);
    const discount = document.getElementById('discount_price').value;
    
    if (discount && parseFloat(discount) >= price) {
        e.preventDefault();
        alert('Discount price must be less than regular price');
        document.getElementById('discount_price').focus();
    }
});
</script>

<?php include __DIR__ . '/../../views/footer.php'; ?>