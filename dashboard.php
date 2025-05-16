<?php
require_once 'includes/functions.php';
requireLogin();

$conn = connectDB();

// Total products
$total_products = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM products");
if ($result) {
    $total_products = $result->fetch_assoc()['total'];
} else {
    // If query fails, set total_products to 0
    $total_products = 0;
}

// Total stock value (admin or manager only)
$total_stock_value = 0;
if (isAdmin() || isManager()) {
    $sql = "SELECT SUM(p.purchase_price * s.quantity) as total 
            FROM products p 
            JOIN stock s ON p.id = s.product_id";
    $result = $conn->query($sql);
    if ($result) {
        $total_stock_value = $result->fetch_assoc()['total'] ?? 0;
    } else {
        $total_stock_value = 0; // Handle query failure
    }
}

// Low stock count (admin or manager only)
$low_stock_count = 0;
if (isAdmin() || isManager()) {
    $sql = "SELECT COUNT(*) as total 
            FROM products p 
            JOIN stock s ON p.id = s.product_id 
            WHERE s.quantity <= 10";
    $result = $conn->query($sql);
    if ($result) {
        $low_stock_count = $result->fetch_assoc()['total'];
    } else {
        $low_stock_count = 0; // Handle query failure
    }
}

// Today's sales (everyone sees)
$sql = "SELECT SUM(total_price) as total 
        FROM sales 
        WHERE DATE(sale_date) = CURDATE()";
$result = $conn->query($sql);
$today_sales = 0;
if ($result) {
    $today_sales = $result->fetch_assoc()['total'] ?? 0;
} else {
    $today_sales = 0; // Handle query failure
}

// Sales visibility logic
$recent_sales = [];
if (isAdmin()) {
    // Admin sees all recent sales
    $sql = "SELECT transaction_id, sale_date, SUM(total_price) as total, COUNT(*) as items 
            FROM sales 
            GROUP BY transaction_id 
            ORDER BY sale_date DESC 
            LIMIT 5";
} else {
    // Manager and normal users see today's sales only
    $sql = "SELECT transaction_id, sale_date, SUM(total_price) as total, COUNT(*) as items 
            FROM sales 
            WHERE DATE(sale_date) = CURDATE() 
            GROUP BY transaction_id 
            ORDER BY sale_date DESC 
            LIMIT 5";
}

$result = $conn->query($sql);
if ($result) {
    $recent_sales = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $recent_sales = []; // Handle query failure or empty result
}

$conn->close();

require_once 'includes/header.php';
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">Dashboard</h1>
    <p class="dashboard-subtitle">Welcome back, <?php echo $_SESSION['username']; ?>! Here's an overview of your inventory.</p>
</div>

<div class="row">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">TOTAL PRODUCTS</div>
                        <div class="stat-card-value"><?php echo $total_products; ?></div>
                        <div class="stat-card-text">Products in inventory</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Value (Admin Only) -->
    <?php if (isAdmin()): ?>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">STOCK VALUE</div>
                        <div class="stat-card-value">$<?php echo number_format($total_stock_value, 2); ?></div>
                        <div class="stat-card-text">Value of inventory</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Today's Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-info h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">TODAY'S SALES</div>
                        <div class="stat-card-value">$<?php echo number_format($today_sales, 2); ?></div>
                        <div class="stat-card-text">Sales for today</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock (Admin/Manager Only) -->
    <?php if (isAdmin() || isManager()): ?>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-danger h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-card-title">LOW STOCK</div>
                        <div class="stat-card-value"><?php echo $low_stock_count; ?></div>
                        <div class="stat-card-text">Items needing restock</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle stat-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Sales Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    <?php echo isAdmin() ? 'Recent Sales (All Time)' : "Today's Sales"; ?>
                </h6>
                <a href="sales.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-list fa-sm mr-1"></i> View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_sales)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent sales found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_sales as $sale): ?>
                                    <tr>
                                        <td><?php echo $sale['transaction_id']; ?></td>
                                        <td><?php echo $sale['items']; ?></td>
                                        <td>$<?php echo number_format($sale['total'], 2); ?></td>
                                        <td><?php echo $sale['sale_date']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-header {
        margin-bottom: 1.5rem;
    }

    .dashboard-title {
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
        color: #858796;
        margin-bottom: 1.5rem;
    }

    .stat-card-text {
        font-size: 0.8rem;
        color: #858796;
        margin-top: 0.25rem;
    }
</style>

<?php require_once 'includes/footer.php'; ?>
