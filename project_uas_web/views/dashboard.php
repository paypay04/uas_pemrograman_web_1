<?php
require_once __DIR__ . '/../config.php';

// Cek login
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Include class files
try {
    $user = new User();
    $product = new Product();
    $order = new Order();
    $category = new Category();
    
    // Total Revenue
    $totalRevenue = $order->getTotalRevenue();
    
    // Total Orders
    $totalOrders = $order->countAll();
    
    // Total Products
    $totalProducts = $product->countAll();
    
    // Total Users (Customers)
    $totalUsers = $user->countAll();
    
    // Low Stock Products (tambahkan method ini ke Product.php jika belum ada)
    $lowStockProducts = 0; // Sementara 0, nanti tambahkan method
    
    // Data untuk chart
    $salesData = $order->getDailySales(7); // 7 hari terakhir
    
    // Category Sales
    $categorySales = $order->getCategorySales();
    
    // Recent Orders (gunakan method getRecent bukan getRecentOrders)
    $recentOrders = $order->getRecent(5);
    
    // Recent Products (gunakan method getRecent bukan getRecentProducts)
    $recentProducts = $product->getRecent(5);
    
    // Recent Users (gunakan method getRecent bukan getRecentUsers)
    $recentUsers = $user->getRecent(5);
    
    // ========== AKHIR PERBAIKAN ==========
    
} catch (Exception $e) {
    die("Error loading dashboard data: " . $e->getMessage());
}

$pageTitle = 'Admin Dashboard';

// Include header
$headerPath = __DIR__ . '/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i><b>Dashboard</b>
            </h1>
            <p class="text-muted">Welcome back, <?php echo $_SESSION['full_name']; ?>! Here's your store overview.</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-calendar-alt me-2"></i>Last 7 Days
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="changeDateRange('7')">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="#" onclick="changeDateRange('30')">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#" onclick="changeDateRange('90')">Last 90 Days</a></li>
                <li><a class="dropdown-item" href="#" onclick="changeDateRange('365')">Last Year</a></li>
            </ul>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total Revenue</h6>
                            <h3 class="mb-0 text-dark">Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>12% from last month
                            </small>
                        </div>
                        <div class="icon-circle bg-primary-light">
                            <i class="fas fa-money-bill-wave text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total Orders</h6>
                            <h3 class="mb-0 text-dark"><?php echo $totalOrders; ?></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>8% from last month
                            </small>
                        </div>
                        <div class="icon-circle bg-success-light">
                            <i class="fas fa-shopping-cart text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total Products</h6>
                            <h3 class="mb-0 text-dark"><?php echo $totalProducts; ?></h3>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-circle me-1"></i><?php echo $lowStockProducts; ?> low stock
                            </small>
                        </div>
                        <div class="icon-circle bg-warning-light">
                            <i class="fas fa-box text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-2">Total Customers</h6>
                            <h3 class="mb-0 text-dark"><?php echo $totalUsers; ?></h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>5% from last month
                            </small>
                        </div>
                        <div class="icon-circle bg-info-light">
                            <i class="fas fa-users text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Sales Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Sales Chart -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Sales by Category
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Row -->
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-clock me-2 text-primary"></i>Recent Orders
                    </h6>
                    <a href="../modules/admin/orders" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_number']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No recent orders</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products & Users -->
        <div class="col-xl-6">
            <div class="row g-4">
                <!-- Recent Products -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark">
                                <i class="fas fa-box me-2 text-primary"></i>Recent Products
                            </h6>
                            <a href="<?php echo BASE_URL; ?>/modules/product" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <?php foreach ($recentProducts as $product): ?>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo BASE_URL; ?>/assets/gambar/<?php echo $product['image_url'] ?? 'default.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="rounded me-3" width="45" height="45">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-muted">Added <?php echo timeAgo($product['created_at']); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-dark">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span><br>
                                            <small class="<?php echo $product['stock'] < 10 ? 'text-danger' : 'text-success'; ?>">
                                                Stock: <?php echo $product['stock']; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($recentProducts)): ?>
                                <div class="text-center py-3 text-muted">No recent products</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/dashboard-charts.js"></script>

<script>
// Initialize charts with PHP data
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart Data from PHP
    const salesData = {
        labels: <?php echo json_encode(array_keys($salesData)); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode(array_values($salesData)); ?>,
            borderColor: '#D2B48C',
            backgroundColor: 'rgba(255, 182, 193, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    // Category Chart Data from PHP
    const categoryData = {
        labels: <?php echo json_encode(array_column($categorySales, 'category')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($categorySales, 'sales')); ?>,
            backgroundColor: [
                '#D2B48C', '#B99976', '#98FB98', '#FFD700', '#87CEEB',
                '#FFA07A', '#9370DB', '#20B2AA', '#F08080', '#6495ED'
            ]
        }]
    };

    // Initialize charts
    initCharts(salesData, categoryData);
});

function changeDateRange(days) {
    // AJAX request to update dashboard data
    fetch(`../modules/admin/dashboard-data.php?days=${days}`)
        .then(response => response.json())
        .then(data => {
            // Update statistics
            updateStatistics(data.stats);
            // Update charts
            updateCharts(data.charts);
            // Update recent data
            updateRecentData(data.recent);
        })
        .catch(error => console.error('Error:', error));
}

function updateStatistics(stats) {
    document.querySelector('[data-stat="revenue"]').textContent = stats.revenue;
    document.querySelector('[data-stat="orders"]').textContent = stats.orders;
    document.querySelector('[data-stat="products"]').textContent = stats.products;
    document.querySelector('[data-stat="users"]').textContent = stats.users;
}
</script>

<?php 
// Helper functions
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'paid' => 'success',
        'unpaid' => 'secondary',
        'refunded' => 'dark'
    ];
    return $colors[strtolower($status)] ?? 'secondary';
}

function timeAgo($datetime) {
    if (empty($datetime)) return 'Unknown';
    
    $time = strtotime($datetime);
    if ($time === false) return 'Invalid date';
    
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff/60) . ' minutes ago';
    if ($diff < 86400) return floor($diff/3600) . ' hours ago';
    if ($diff < 2592000) return floor($diff/86400) . ' days ago';
    if ($diff < 31536000) return floor($diff/2592000) . ' months ago';
    return floor($diff/31536000) . ' years ago';
}
?>

<style>
.stat-card {
    border-radius: 15px;
    transition: transform 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-primary-light { background-color: rgba(255, 182, 193, 0.1); }
.bg-success-light { background-color: rgba(152, 251, 152, 0.1); }
.bg-warning-light { background-color: rgba(255, 215, 0, 0.1); }
.bg-info-light { background-color: rgba(135, 206, 235, 0.1); }
.chart-container {
    position: relative;
    height: 300px;
}
.avatar-placeholder {
    font-weight: bold;
}
</style>

<?php 
// Pastikan footer.php ada di folder yang sama
$footerPath = realpath(__DIR__ . '/footer.php');
if ($footerPath) {
    include $footerPath;
} else {
    echo '<!-- Footer not found -->';
}
?>