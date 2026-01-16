<?php
require_once __DIR__ . '/../../config.php';

// Cek login
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Include class User
try {
    $user = new User();
    
    // Get all users
    $users = $user->getAll();
    
    // Hitung total users
    $totalUsers = count($users);
    
    // Hitung jumlah admin dan user
    $adminCount = count(array_filter($users, function($u) { return $u['role'] === 'admin'; }));
    $userCount = count(array_filter($users, function($u) { return $u['role'] === 'user'; }));
    
    // Filter berdasarkan role (opsional)
    $filterRole = isset($_GET['role']) ? $_GET['role'] : '';
    if ($filterRole && in_array($filterRole, ['admin', 'user'])) {
        $users = array_filter($users, function($user) use ($filterRole) {
            return $user['role'] === $filterRole;
        });
    }
    
    // Search functionality
    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
    if (!empty($searchQuery)) {
        $users = array_filter($users, function($user) use ($searchQuery) {
            return stripos($user['username'], $searchQuery) !== false ||
                   stripos($user['email'], $searchQuery) !== false ||
                   stripos($user['full_name'], $searchQuery) !== false;
        });
    }
    
} catch (Exception $e) {
    die("Error loading user data: " . $e->getMessage());
}

$pageTitle = 'Manage Users - Harum Bakery Admin';

// Include header
$headerPath = __DIR__ . '/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>

<div class="harum-admin-container">
    <!-- SIDEBAR -->
    <div class="harum-sidebar">
        <div class="sidebar-header">
            <h1 class="sidebar-title">
                <i class="fas fa-crown me-2"></i>Harum Bakery Admin
            </h1>
            <p class="sidebar-subtitle">Manage User Accounts</p>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">
                <?php 
                $adminInitials = '';
                if (!empty($_SESSION['full_name'])) {
                    $nameParts = explode(' ', $_SESSION['full_name']);
                    foreach ($nameParts as $part) {
                        $adminInitials .= strtoupper(substr($part, 0, 1));
                        if (strlen($adminInitials) >= 2) break;
                    }
                } else {
                    $adminInitials = 'AD';
                }
                echo $adminInitials;
                ?>
            </div>
            <div class="user-info">
                <h4><?php echo $_SESSION['full_name']; ?></h4>
                <p>Administrator</p>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="?" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Manage Users</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="harum-main-content">
        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $adminCount; ?></div>
                <div class="stat-label">Admins</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $userCount; ?></div>
                <div class="stat-label">Customers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalUsers; ?></div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>

        <!-- USER MANAGEMENT SECTION -->
        <div class="content-card">
            <div class="card-header">
                <div class="header-left">
                    <h2>User Management</h2>
                    <p>Manage all registered users on the bakery system</p>
                </div>
                <div class="header-right">
                    <a href="<?php echo BASE_URL; ?>/dashboard" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- SEARCH AND FILTER -->
                <div class="tools-row">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <form method="GET" action="" class="w-100">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search users..."
                                   value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button type="submit" class="search-btn">Search</button>
                        </form>
                    </div>
                    
                    <div class="filter-tabs">
                        <a href="?" class="filter-tab <?php echo empty($filterRole) ? 'active' : ''; ?>">
                            All Users
                        </a>
                        <a href="?role=admin" class="filter-tab <?php echo $filterRole === 'admin' ? 'active' : ''; ?>">
                            <i class="fas fa-user-shield"></i> Admins
                        </a>
                        <a href="?role=user" class="filter-tab <?php echo $filterRole === 'user' ? 'active' : ''; ?>">
                            <i class="fas fa-user"></i> Customers
                        </a>
                    </div>
                </div>

                <!-- USERS TABLE -->
                <div class="users-table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $userItem): ?>
                                <tr>
                                    <td class="user-id">#<?php echo $userItem['id']; ?></td>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">
                                                <?php 
                                                $initials = '';
                                                if (!empty($userItem['full_name'])) {
                                                    $nameParts = explode(' ', $userItem['full_name']);
                                                    foreach ($nameParts as $part) {
                                                        $initials .= strtoupper(substr($part, 0, 1));
                                                        if (strlen($initials) >= 2) break;
                                                    }
                                                } else {
                                                    $initials = strtoupper(substr($userItem['username'], 0, 2));
                                                }
                                                echo $initials;
                                                ?>
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name"><?php echo htmlspecialchars($userItem['full_name'] ?: $userItem['username']); ?></div>
                                                <div class="user-username">@<?php echo htmlspecialchars($userItem['username']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="user-email"><?php echo htmlspecialchars($userItem['email']); ?></td>
                                    <td class="user-phone"><?php echo htmlspecialchars($userItem['phone'] ?: '-'); ?></td>
                                    <td>
                                        <span class="role-badge <?php echo $userItem['role'] === 'admin' ? 'role-admin' : 'role-user'; ?>">
                                            <?php echo ucfirst($userItem['role']); ?>
                                        </span>
                                    </td>
                                    <td class="user-date"><?php echo date('d M Y', strtotime($userItem['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" onclick="viewUserDetails(<?php echo $userItem['id']; ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit-btn" onclick="editUser(<?php echo $userItem['id']; ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $userItem['id']; ?>)" title="Delete" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <div class="empty-state">
                                            <i class="fas fa-users"></i>
                                            <h3>No users found</h3>
                                            <p><?php echo !empty($searchQuery) || !empty($filterRole) ? 'Try changing your search or filter' : 'No users have registered yet'; ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABLE FOOTER -->
                <div class="table-footer">
                    <div class="footer-left">
                        Showing <strong><?php echo count($users); ?></strong> of <strong><?php echo $totalUsers; ?></strong> users
                    </div>
                    <div class="footer-right">
                        <span class="info-text">
                            <i class="fas fa-info-circle"></i> Edit/Delete coming soon
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FOR USER DETAILS -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewUserDetails(userId) {
    document.getElementById('userDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    modal.show();
    
    fetch(`<?php echo BASE_URL; ?>/modules/admin/user-details.php?id=${userId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('userDetailsContent').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('userDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading user details.
                </div>
            `;
        });
}

function editUser(userId) {
    alert('Edit feature coming soon!');
}

function deleteUser(userId) {
    alert('Delete feature currently disabled.');
}
</script>

<style>
/* HARUM BAKERY ADMIN STYLES - Simple & Clean */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #f8f9fa;
}

/* CONTAINER */
.harum-admin-container {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */
.harum-sidebar {
    width: 280px;
    background: linear-gradient(180deg, #5E3023 0%, #895737 100%);
    color: white;
    padding: 30px 25px;
    position: fixed;
    height: 100vh;
    left: 0;
    top: 0;
}

.sidebar-header {
    margin-bottom: 40px;
}

.sidebar-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
    color: white;
}

.sidebar-subtitle {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
}

.sidebar-user {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 20px;
    color: #5E3023;
}

.user-info h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.user-info p {
    margin: 5px 0 0 0;
    opacity: 0.7;
    font-size: 0.9rem;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nav-item {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s;
    font-weight: 500;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.nav-item.active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    font-weight: 600;
}

.nav-item i {
    width: 20px;
    text-align: center;
}

/* MAIN CONTENT */
.harum-main-content {
    flex: 1;
    margin-left: 280px;
    padding: 30px;
}

/* STATS CARDS */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid #eaeaea;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #5E3023;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.95rem;
}

/* CONTENT CARD */
.content-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.card-header {
    padding: 25px 30px;
    border-bottom: 1px solid #eaeaea;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafafa;
}

.header-left h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 5px 0;
}

.header-left p {
    color: #666;
    margin: 0;
    font-size: 0.95rem;
}

.back-btn {
    background: #895737;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: background 0.3s;
}

.back-btn:hover {
    background: #5E3023;
    color: white;
    text-decoration: none;
}

.card-body {
    padding: 30px;
}

/* TOOLS ROW */
.tools-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    gap: 20px;
}

.search-box {
    flex: 1;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #ddd;
}

.search-box i {
    color: #666;
}

.search-box form {
    display: flex;
    flex: 1;
    gap: 10px;
}

.search-box input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.95rem;
}

.search-btn {
    background: #895737;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
}

.filter-tabs {
    display: flex;
    gap: 10px;
}

.filter-tab {
    padding: 8px 20px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #666;
    font-weight: 500;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    gap: 5px;
}

.filter-tab:hover {
    background: #e9ecef;
    color: #333;
    text-decoration: none;
}

.filter-tab.active {
    background: #895737;
    color: white;
    border-color: #895737;
}

/* TABLE */
.users-table-container {
    overflow-x: auto;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table thead {
    background: #f8f9fa;
}

.users-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.users-table td {
    padding: 15px;
    border-bottom: 1px solid #eaeaea;
    vertical-align: middle;
}

.users-table tbody tr:hover {
    background: #f8f9fa;
}

/* USER CELLS */
.user-info-cell {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar-small {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #87CEEB 0%, #6CB2EB 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: #333;
}

.user-username {
    font-size: 0.85rem;
    color: #666;
}

.user-id {
    font-weight: 600;
    color: #895737;
}

.user-email, .user-phone, .user-date {
    color: #555;
    font-size: 0.95rem;
}

/* BADGES */
.role-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}

.role-admin {
    background: rgba(255, 107, 107, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.role-user {
    background: rgba(137, 87, 55, 0.1);
    color: #895737;
    border: 1px solid rgba(137, 87, 55, 0.2);
}

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    gap: 5px;
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 6px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.9rem;
}

.view-btn {
    background: rgba(0, 123, 255, 0.1);
    color: #007bff;
}

.view-btn:hover {
    background: #007bff;
    color: white;
}

.edit-btn {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.edit-btn:hover {
    background: #28a745;
    color: white;
}

.delete-btn {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    opacity: 0.5;
    cursor: not-allowed;
}

/* TABLE FOOTER */
.table-footer {
    padding: 20px 0 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #eaeaea;
    margin-top: 20px;
    color: #666;
    font-size: 0.9rem;
}

.info-text {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* EMPTY STATE */
.no-data {
    text-align: center;
    padding: 60px 20px !important;
}

.empty-state {
    color: #666;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.3;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #333;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .harum-sidebar {
        display: none;
    }
    
    .harum-main-content {
        margin-left: 0;
    }
    
    .tools-row {
        flex-direction: column;
        align-items: stretch;
    }
}

@media (max-width: 768px) {
    .harum-main-content {
        padding: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .users-table th:nth-child(3),
    .users-table td:nth-child(3),
    .users-table th:nth-child(4),
    .users-table td:nth-child(4) {
        display: none;
    }
}
</style>

<?php 
// Include footer
$footerPath = __DIR__ . '/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    echo '<!-- Footer not found -->';
}
?>