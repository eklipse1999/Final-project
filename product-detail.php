<?php
require_once 'includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? sanitize($_GET['id']) : null;

if (!$id) {
    header("Location: products.php");
    exit;
}

$product = getProductById($id);

if (!$product) {
    header("Location: products.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="fas fa-box mr-2"></i>Product Details</h1>
        <p class="page-subtitle">View detailed information about this product</p>
    </div>
    <a href="products.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Products
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle mr-2"></i><?php echo $product['name']; ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <span class="product-info-label">SKU:</span>
                            <span class="product-info-value"><?php echo $product['sku']; ?></span>
                        </div>
                        <div class="product-info-item">
                            <span class="product-info-label">Price:</span>
                            <span class="product-info-value">$<?php echo number_format($product['selling_price'], 2); ?></span>
                        </div>
                        <div class="product-info-item">
                            <span class="product-info-label">Quantity in Stock:</span>
                            <span class="product-info-value">
                                <?php 
                                if ($product['quantity'] <= 10) {
                                    echo '<span class="badge badge-danger">' . $product['quantity'] . '</span>';
                                } else {
                                    echo '<span class="badge badge-success">' . $product['quantity'] . '</span>';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="product-info-item">
                            <span class="product-info-label">Total Value:</span>
                            <span class="product-info-value">$<?php echo number_format($product['selling_price'] * $product['quantity'], 2); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="product-info-item">
                            <span class="product-info-label">Description:</span>
                            <div class="product-description">
                                <?php echo $product['description'] ? $product['description'] : 'No description available.'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="btn-group">
                    <?php if (hasRole('manager')): ?>
                        <a href="product-form.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </a>
                    <?php endif; ?>
                    <?php if (hasRole('user')): ?>
                        <a href="record-sale.php?id=<?php echo $product['id']; ?>" class="btn btn-success">
                            <i class="fas fa-shopping-cart mr-2"></i>Record Sale
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-bar mr-2"></i>Stock Status</h6>
            </div>
            <div class="card-body text-center">
                <?php 
                $percentage = $product['quantity'] > 0 ? min(100, ($product['quantity'] / 50) * 100) : 0;
                $color = $product['quantity'] <= 10 ? 'danger' : ($product['quantity'] <= 25 ? 'warning' : 'success');
                ?>
                <div class="stock-chart">
                    <div class="stock-chart-value"><?php echo $product['quantity']; ?></div>
                    <div class="progress">
                        <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $product['quantity']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="stock-status mt-3">
                    <?php if ($product['quantity'] <= 10): ?>
                        <div class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Low Stock</div>
                    <?php elseif ($product['quantity'] <= 25): ?>
                        <div class="text-warning"><i class="fas fa-exclamation-circle mr-2"></i>Medium Stock</div>
                    <?php else: ?>
                        <div class="text-success"><i class="fas fa-check-circle mr-2"></i>Good Stock</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-info-item {
        margin-bottom: 1.5rem;
    }
    
    .product-info-label {
        display: block;
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    .product-info-value {
        font-size: 1.1rem;
        color: #5a5c69;
    }
    
    .product-description {
        background-color: #f8f9fc;
        padding: 1rem;
        border-radius: 0.5rem;
        color: #5a5c69;
    }
    
    .stock-chart {
        position: relative;
        padding: 2rem 0;
    }
    
    .stock-chart-value {
        font-size: 3rem;
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 1rem;
    }
    
    .progress {
        height: 1.5rem;
        border-radius: 0.5rem;
        background-color: #eaecf4;
    }
    
    .stock-status {
        font-size: 1.1rem;
        font-weight: 600;
    }
</style>

<?php require_once 'includes/footer.php'; ?>

