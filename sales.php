<?php
require_once 'includes/functions.php';
requireLogin();

// Get sales data
$sales = getSalesReport();

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="fas fa-shopping-cart mr-2"></i>Sales History</h1>
        <p class="page-subtitle">View all sales transactions</p>
    </div>
    <?php if (hasRole('admin')): ?>
        <a href="reports.php" class="btn btn-primary">
            <i class="fas fa-chart-bar mr-2"></i>Generate Reports
        </a>
    <?php endif; ?>
</div>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-list mr-2"></i>Sales Transactions</h6>
    </div>
    <div class="card-body">
        <?php if (empty($sales)): ?>
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">No sales found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Sold By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo $sale['id']; ?></td>
                                <td><?php echo $sale['name']; ?></td>
                                <td><?php echo $sale['quantity']; ?></td>
                                <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                                <td><?php echo $sale['sale_date']; ?></td>
                                <td><?php echo $sale['username']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .page-title {
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: #858796;
        margin-bottom: 0;
    }
</style>

<?php require_once 'includes/footer.php'; ?>

