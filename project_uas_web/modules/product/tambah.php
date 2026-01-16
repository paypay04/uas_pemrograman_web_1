<?php
// modules/product/tambah.php
require_once __DIR__ . '/../../config.php';

// Cek login dan role admin
if (!isLoggedIn()) {
    redirect('/login');
    exit();
}

if (!isAdmin()) {
    $_SESSION['error'] = 'Access denied. Admin only.';
    redirect('/');
    exit();
}

require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/Product.php';
require_once __DIR__ . '/../../class/Category.php';

$product = new Product();
$category = new Category();

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => $_POST['price'] ?? 0,
        'discount_price' => !empty($_POST['discount_price']) ? $_POST['discount_price'] : null,
        'category_id' => $_POST['category_id'] ?? null,
        'stock' => $_POST['stock'] ?? 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active' => isset($_POST['is_active']) ? 1 : 1,
        'image_url' => ''
    ];
    
    // Validasi
    if (empty($data['name'])) {
        $errors['name'] = 'Product name is required';
    }
    
    if (empty($data['price']) || $data['price'] <= 0) {
        $errors['price'] = 'Valid price is required';
    }
    
    if ($data['discount_price'] && $data['discount_price'] >= $data['price']) {
        $errors['discount_price'] = 'Discount price must be lower than regular price';
    }
    
    if ($data['stock'] < 0) {
        $errors['stock'] = 'Stock cannot be negative';
    }
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        
        // Check file type
        if (!in_array($file_type, $allowed_types)) {
            $errors['image'] = 'Only JPG, PNG, GIF, and WebP images are allowed';
        }
        
        // Check file size
        if ($file_size > $max_size) {
            $errors['image'] = 'Image size must be less than 2MB';
        }
        
        if (empty($errors)) {
            // Generate unique filename
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = __DIR__ . '/../../assets/gambar/products/' . $new_filename;
            
            // Create directory if not exists
            if (!file_exists(dirname($upload_path))) {
                mkdir(dirname($upload_path), 0777, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $data['image_url'] = 'products/' . $new_filename;
            } else {
                $errors['image'] = 'Failed to upload image';
            }
        }
    } else {
        // No image uploaded, use default
        $data['image_url'] = 'default.jpg';
    }
    
    // If no errors, save product
    if (empty($errors)) {
        if ($product->create($data)) {
            $success = true;
            $_SESSION['success'] = 'Product added successfully!';
            
            // Redirect after 2 seconds
            header('Refresh: 2; URL=' . BASE_URL . '/products');
        } else {
            $errors['general'] = 'Failed to add product. Please try again.';
        }
    }
}

// Get categories for dropdown
$categories = $category->getAll();

$pageTitle = 'Add New Product';
include __DIR__ . '/../../views/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/products">Products</a></li>
            <li class="breadcrumb-item active">Add Product</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 fw-bold text-dark">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Add New Product
            </h1>
            <p class="text-muted">Fill the form below to add a new product</p>
        </div>
    </div>
    
    <!-- Success Message -->
    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success!</strong> Product added successfully. Redirecting to products page...
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Error Messages -->
    <?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($errors['general']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Product Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="" enctype="multipart/form-data" id="productForm">
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="text-dark mb-3">
                                <i class="fas fa-info-circle me-2 text-primary"></i>Basic Information
                            </h5>
                            
                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                       required
                                       placeholder="e.g., Basque Cheesecake">
                                <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['name']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Describe your product..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                <div class="form-text">Describe the product features, ingredients, etc.</div>
                            </div>
                            
                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="mb-4">
                            <h5 class="text-dark mb-3">
                                <i class="fas fa-tag me-2 text-primary"></i>Pricing & Stock
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Regular Price -->
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-bold">Regular Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" 
                                               id="price" 
                                               name="price" 
                                               min="0" 
                                               step="100" 
                                               value="<?php echo $_POST['price'] ?? ''; ?>" 
                                               required
                                               placeholder="0">
                                        <?php if (isset($errors['price'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo htmlspecialchars($errors['price']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Discount Price -->
                                <div class="col-md-6">
                                    <label for="discount_price" class="form-label fw-bold">Discount Price (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control <?php echo isset($errors['discount_price']) ? 'is-invalid' : ''; ?>" 
                                               id="discount_price" 
                                               name="discount_price" 
                                               min="0" 
                                               step="100" 
                                               value="<?php echo $_POST['discount_price'] ?? ''; ?>"
                                               placeholder="Leave empty for no discount">
                                        <?php if (isset($errors['discount_price'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo htmlspecialchars($errors['discount_price']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-text">Leave empty if no discount</div>
                                </div>
                                
                                <!-- Stock -->
                                <div class="col-md-6">
                                    <label for="stock" class="form-label fw-bold">Stock Quantity</label>
                                    <input type="number" 
                                           class="form-control <?php echo isset($errors['stock']) ? 'is-invalid' : ''; ?>" 
                                           id="stock" 
                                           name="stock" 
                                           min="0" 
                                           value="<?php echo $_POST['stock'] ?? 0; ?>">
                                    <?php if (isset($errors['stock'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['stock']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Image -->
                        <div class="mb-4">
                            <h5 class="text-dark mb-3">
                                <i class="fas fa-image me-2 text-primary"></i>Product Image
                            </h5>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label fw-bold">Product Image</label>
                                <input type="file" 
                                       class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" 
                                       id="image" 
                                       name="image"
                                       accept="gambar/*">
                                <?php if (isset($errors['image'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['image']); ?>
                                </div>
                                <?php endif; ?>
                                <div class="form-text">
                                    Recommended: Square image, max 2MB. Allowed: JPG, PNG, GIF, WebP
                                </div>
                            </div>
                            
                            <!-- Image Preview -->
                            <div class="image-preview mt-3 d-none">
                                <h6 class="text-dark mb-2">Preview:</h6>
                                <img id="imagePreview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                        
                        <!-- Product Settings -->
                        <div class="mb-4">
                            <h5 class="text-dark mb-3">
                                <i class="fas fa-cog me-2 text-primary"></i>Product Settings
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_featured" 
                                               name="is_featured" 
                                               value="1"
                                               <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                    <div class="form-text">Featured products will be highlighted</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               <?php echo !isset($_POST['is_active']) || $_POST['is_active'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="is_active">
                                            Active Status
                                        </label>
                                    </div>
                                    <div class="form-text">Inactive products won't be shown</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Buttons -->
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Product
                            </button>
                            <button type="reset" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                            <a href="<?php echo BASE_URL; ?>/product" class="btn btn-outline-danger px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Tips -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="text-dark mb-3">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Tips for Adding Products
                    </h5>
                    
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex">
                                <i class="fas fa-check text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-bold">Use Descriptive Names</small>
                                    <p class="text-muted mb-0">Include key features in product name</p>
                                </div>
                            </div>
                        </li>
                        
                        <li class="mb-3">
                            <div class="d-flex">
                                <i class="fas fa-check text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-bold">High-Quality Images</small>
                                    <p class="text-muted mb-0">Use clear, well-lit product photos</p>
                                </div>
                            </div>
                        </li>
                        
                        <li class="mb-3">
                            <div class="d-flex">
                                <i class="fas fa-check text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-bold">Accurate Pricing</small>
                                    <p class="text-muted mb-0">Double-check prices before saving</p>
                                </div>
                            </div>
                        </li>
                        
                        <li class="mb-3">
                            <div class="d-flex">
                                <i class="fas fa-check text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-bold">Stock Management</small>
                                    <p class="text-muted mb-0">Keep stock counts updated regularly</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <hr class="my-4">
                    
                    <h6 class="text-dark mb-3">Required Fields</h6>
                    <div class="alert alert-light">
                        <p class="mb-2"><span class="text-danger">*</span> indicates required fields</p>
                        <p class="mb-0">All other fields are optional but recommended for better product display.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Form Enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.querySelector('.image-preview');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Auto calculate discount percentage
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('discount_price');
    
    function calculateDiscount() {
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        
        if (price > 0 && discount > 0 && discount < price) {
            const percentage = Math.round(((price - discount) / price) * 100);
            
            // Show discount info
            let discountInfo = document.getElementById('discountInfo');
            if (!discountInfo) {
                discountInfo = document.createElement('div');
                discountInfo.id = 'discountInfo';
                discountInfo.className = 'form-text text-success fw-bold';
                discountInput.parentNode.appendChild(discountInfo);
            }
            discountInfo.textContent = `Discount: ${percentage}% off`;
        } else {
            const discountInfo = document.getElementById('discountInfo');
            if (discountInfo) {
                discountInfo.remove();
            }
        }
    }
    
    if (priceInput && discountInput) {
        priceInput.addEventListener('input', calculateDiscount);
        discountInput.addEventListener('input', calculateDiscount);
    }
    
    // Form validation
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const price = parseFloat(priceInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            
            if (discount > 0 && discount >= price) {
                e.preventDefault();
                alert('Discount price must be lower than regular price');
                discountInput.focus();
                return false;
            }
            
            // Show loading
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>

<style>
.card {
    border-radius: 12px;
    overflow: hidden;
}

.form-label {
    font-size: 0.9rem;
    color: #495057;
}

.form-control:focus, .form-select:focus {
    border-color: #FFB6C1;
    box-shadow: 0 0 0 0.25rem rgba(255, 182, 193, 0.25);
}

.form-check-input:checked {
    background-color: #FFB6C1;
    border-color: #FFB6C1;
}

.image-preview img {
    object-fit: cover;
    border: 2px dashed #dee2e6;
    padding: 5px;
}

.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}
</style>

<?php include __DIR__ . '/../../views/footer.php'; ?>