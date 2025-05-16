<?php
require_once 'includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? sanitize($_GET['id']) : null;
$success = '';
$error = '';

if (!$id) {
    header("Location: products.php");
    exit;
}

$product = getProductById($id);

if (!$product) {
    header("Location: products.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = sanitize($_POST['quantity']);
    
    if (empty($quantity) || $quantity <= 0) {
        $error = "Please enter a valid quantity";
    } elseif ($quantity > $product['quantity']) {
        $error = "Not enough stock available";
    } else {
        if (recordSale($id, $quantity, $_SESSION['user_id'])) {
            $success = "Sale recorded successfully";
            // Refresh product data
            $product = getProductById($id);
        } else {
            $error = "Error recording sale";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="fas fa-shopping-cart mr-2"></i>Record Sale</h1>
        <p class="page-subtitle">Record a new sale for this product</p>
    </div>
    <a href="products.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Products
    </a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-box mr-2"></i><?php echo $product['name']; ?></h6>
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
                            <span class="product-info-label">Available Quantity:</span>
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
                    </div>
                    <div class="col-md-6">
                        <form method="post" class="sale-form">
                            <div class="form-group">
                                <label for="quantity">Quantity to Sell</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" value="1" required>
                                <small class="form-text text-muted">Maximum available: <?php echo $product['quantity']; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="total">Total Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="text" class="form-control" id="total" value="<?php echo number_format($product['selling_price'] , 2); ?>" readonly>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-shopping-cart mr-2"></i>Record Sale
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Sale Information</h6>
            </div>
            <div class="card-body">
                <div class="sale-info">
                    <p><i class="fas fa-user mr-2"></i>Recorded by: <strong><?php echo $_SESSION['username']; ?></strong></p>
                    <p><i class="fas fa-calendar mr-2"></i>Date: <strong><?php echo date('Y-m-d H:i:s'); ?></strong></p>
                    <hr>
                    <p class="text-muted">Recording a sale will automatically update the inventory quantity.</p>
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
    
    .sale-form {
        background-color: #f8f9fc;
        padding: 1.5rem;
        border-radius: 0.5rem;
    }
    
    .sale-info {
        color: #5a5c69;
    }
    
    .sale-info p {
        margin-bottom: 1rem;
    }
    
    .sale-info i {
        color: var(--primary-color);
    }
</style>

<script>
document.getElementById('quantity').addEventListener('input', function() {
    var quantity = this.value;
    var price = <?php echo $product['selling_price']; ?>;
    var total = quantity * price;
    document.getElementById('total').value = total.toFixed(2);
});
</script>

<?php require_once 'includes/footer.php'; ?>

