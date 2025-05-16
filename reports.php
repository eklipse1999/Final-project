<?php
require_once 'includes/functions.php';
requireLogin();
requireRole('admin');

$start_date = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : '';
$report_type = isset($_GET['report_type']) ? sanitize($_GET['report_type']) : '';

// Initialize data arrays
$sales_data = [];
$low_stock_data = [];

// Generate report if form submitted
if ($report_type) {
    if ($report_type == 'sales' && $start_date && $end_date) {
        $sales_data = getSalesReport($start_date, $end_date);
    } elseif ($report_type == 'low_stock') {
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;
        $low_stock_data = getLowStockProducts($threshold);
    }
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
      <h1 class="page-title"><i class="fas fa-chart-bar mr-2"></i>Reports</h1>
      <p class="page-subtitle">Generate and print inventory reports</p>
  </div>
  <div>
      <span class="badge badge-info p-2"><i class="fas fa-keyboard mr-1"></i> Press <kbd>Ctrl</kbd> + <kbd>P</kbd> to print report</span>
  </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart mr-2"></i>Sales Report</h5>
            </div>
            <div class="card-body">
                <form method="get" action="reports.php">
                    <input type="hidden" name="report_type" value="sales">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Report</h5>
            </div>
            <div class="card-body">
                <form method="get" action="reports.php">
                    <input type="hidden" name="report_type" value="low_stock">
                    <div class="form-group">
                        <label for="threshold">Threshold</label>
                        <input type="number" class="form-control" id="threshold" name="threshold" min="1" value="10" required>
                        <small class="form-text text-muted">Show products with stock below this value</small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>Generate Report
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($report_type == 'sales' && !empty($sales_data)): ?>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-file-alt mr-2"></i>Sales Report (<?php echo $start_date; ?> to <?php echo $end_date; ?>)</h5>
            <button class="btn btn-sm btn-info print-btn" onclick="window.print()">
                <i class="fas fa-print mr-2"></i>Print Report
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
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
                        <?php 
                        $total_sales = 0;
                        foreach ($sales_data as $sale): 
                            $total_sales += $sale['total_price'];
                        ?>
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
                    <tfoot>
                        <tr>
                            <th colspan="3">Total Sales</th>
                            <th>$<?php echo number_format($total_sales, 2); ?></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php elseif ($report_type == 'sales' && empty($sales_data)): ?>
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle mr-2"></i>No sales data found for the selected period.
    </div>
<?php endif; ?>

<?php if ($report_type == 'low_stock' && !empty($low_stock_data)): ?>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-file-alt mr-2"></i>Low Stock Report (Threshold: <?php echo isset($_GET['threshold']) ? $_GET['threshold'] : 10; ?>)</h5>
            <button class="btn btn-sm btn-info print-btn" onclick="window.print()">
                <i class="fas fa-print mr-2"></i>Print Report
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock_data as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['sku']; ?></td>
                                <td>$<?php echo number_format($product['selling_price'], 2); ?></td>
                                <td class="text-danger"><?php echo $product['quantity']; ?></td>
                                <td>$<?php echo number_format($product['selling_price'] * $product['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif ($report_type == 'low_stock' && empty($low_stock_data)): ?>
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle mr-2"></i>No products below the stock threshold.
    </div>
<?php endif; ?>

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
  
  @media (max-width: 768px) {
      .card-header {
          flex-direction: column;
          align-items: flex-start !important;
      }
      
      .card-header .btn {
          margin-top: 10px;
      }
  }
  
  /* Print styles */
  @media print {
      .navbar, .footer, form, .btn, .no-print, .print-btn {
          display: none !important;
      }
      
      .card {
          border: none !important;
          box-shadow: none !important;
      }
      
      .card-header {
          background-color: #f8f9fc !important;
          color: #000 !important;
          border-bottom: 1px solid #ddd !important;
      }
      
      body {
          padding: 0 !important;
          margin: 0 !important;
      }
      
      .container {
          max-width: 100% !important;
          width: 100% !important;
          padding: 0 !important;
          margin: 0 !important;
      }
      
      table {
          width: 100% !important;
      }
      
      .table-responsive {
          overflow: visible !important;
      }
  }
</style>

<script>
document.addEventListener('keydown', function(e) {});
</script>

<?php require_once 'includes/footer.php'; ?>

