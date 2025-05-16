<?php
require_once 'includes/functions.php';
requireLogin();

// Connect to the database
$conn = connectDB();

if (hasRole('admin')) {
    $sales = getGroupedSalesReport($conn); // No filter
} else {
    $today = date('Y-m-d');
    $sales = getGroupedSalesReport($conn, $today); // Filter by today
}

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
                            <th>Transaction ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Date</th>
                            <th>Sold By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $transaction): ?>
                            <?php foreach ($transaction['products'] as $sale): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['name']); ?></td>
                                    <td><?php echo (int)$sale['quantity']; ?></td>
                                    <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                                    <td><?php echo htmlspecialchars($sale['username']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-success">
                                <td></td>
                                <td colspan="2" class="text-right font-weight-bold">Total for Transaction:</td>
                                <td colspan="3" class="font-weight-bold">$<?php echo number_format($transaction['total'], 2); ?></td>
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
