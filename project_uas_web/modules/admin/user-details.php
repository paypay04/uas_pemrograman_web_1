<?php
require_once __DIR__ . '/../../config.php';

// Cek login
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die('Unauthorized');
}

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

// Get user ID from request
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId <= 0) {
    die('<div class="alert alert-danger">Invalid user ID</div>');
}

try {
    $user = new User();
    $userData = $user->getById($userId);
    
    if (!$userData) {
        die('<div class="alert alert-danger">User not found</div>');
    }
    
    // Format dates
    $createdDate = date('d F Y H:i', strtotime($userData['created_at']));
    $updatedDate = date('d F Y H:i', strtotime($userData['updated_at']));
    
} catch (Exception $e) {
    die('<div class="alert alert-danger">Error loading user data: ' . htmlspecialchars($e->getMessage()) . '</div>');
}
?>

<div class="row">
    <!-- User Info -->
    <div class="col-md-4">
        <div class="text-center mb-4">
            <div class="avatar-placeholder rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 100px; height: 100px; font-size: 36px;">
                <?php 
                $initials = '';
                if (!empty($userData['full_name'])) {
                    $nameParts = explode(' ', $userData['full_name']);
                    foreach ($nameParts as $part) {
                        $initials .= strtoupper(substr($part, 0, 1));
                        if (strlen($initials) >= 2) break;
                    }
                } else {
                    $initials = strtoupper(substr($userData['username'], 0, 2));
                }
                echo $initials;
                ?>
            </div>
            <h4 class="mb-1"><?php echo htmlspecialchars($userData['full_name'] ?: $userData['username']); ?></h4>
            <p class="text-muted mb-2">@<?php echo htmlspecialchars($userData['username']); ?></p>
            <span class="badge bg-<?php echo $userData['role'] === 'admin' ? 'danger' : 'primary'; ?> fs-6">
                <i class="fas fa-<?php echo $userData['role'] === 'admin' ? 'user-shield' : 'user'; ?> me-1"></i>
                <?php echo ucfirst($userData['role']); ?>
            </span>
        </div>
        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="fas fa-id-card me-2 text-primary"></i>User ID</h6>
                <p class="mb-0">
                    <code class="bg-light p-2 rounded d-block">#<?php echo $userData['id']; ?></code>
                </p>
            </div>
        </div>
    </div>
    
    <!-- User Details -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Account Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-1">Full Name</label>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($userData['full_name'] ?: 'Not set'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-1">Username</label>
                        <p class="mb-0 fw-bold">@<?php echo htmlspecialchars($userData['username']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-1">Email Address</label>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($userData['email']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small mb-1">Phone Number</label>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($userData['phone'] ?: 'Not set'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Address</h6>
                <?php if (!empty($userData['address'])): ?>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($userData['address'])); ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0">No address provided</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="fas fa-calendar-plus me-2 text-primary"></i>Account Created</h6>
                        <p class="mb-0">
                            <i class="fas fa-clock me-2 text-muted"></i>
                            <?php echo $createdDate; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="fas fa-calendar-check me-2 text-primary"></i>Last Updated</h6>
                        <p class="mb-0">
                            <i class="fas fa-clock me-2 text-muted"></i>
                            <?php echo $updatedDate; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 pt-3 border-top">
    <small class="text-muted">
        <i class="fas fa-exclamation-circle me-1"></i>
        Note: Edit and Delete functions are disabled in READ-ONLY mode.
    </small>
</div>