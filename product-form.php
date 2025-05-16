<?php
require_once 'includes/functions.php';
requireLogin();
requireRole('manager');

$id = isset($_GET['id']) ? sanitize($_GET['id']) : null;
$product = null;
$success = '';
$error = '';

// Get product if editing
if ($id) {
    $product = getProductById($id);
    if (!$product) {
        header("Location: products.php");
        exit;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $sku = sanitize($_POST['sku'] ?? '');
    $purchase_price = sanitize($_POST['purchase_price'] ?? '');
    $unit_price = sanitize($_POST['unit_price'] ?? '');
    $selling_price = sanitize($_POST['selling_price'] ?? '');
    $supplier = sanitize($_POST['supplier'] ?? '');
    $status = sanitize($_POST['status'] ?? '');

    // Validate required fields
    if (empty($name) || empty($sku) || empty($purchase_price) || empty($unit_price) || empty($selling_price) || empty($status)) {
        $error = "Please fill in all required fields";
    } else {
        if ($id) {
            // Update product
            if (updateProduct($id, $name, $description, $category, $sku, $purchase_price, $unit_price, $selling_price, $supplier, $status)) {
                $success = "Product updated successfully";
                $product = getProductById($id);
            } else {
                $error = "Error updating product";
            }
        } else {
            // Add new product
            if (addProduct($name, $description, $category, $sku, $purchase_price, $unit_price, $selling_price, $supplier, $status)) {
                $success = "Product added successfully";
                // Clear form
                $name = $description = $category = $sku = $purchase_price = $unit_price = $selling_price = $supplier = $status = '';
            } else {
                $error = "Error adding product";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $id ? 'Edit' : 'Add'; ?> Product</h1>
    <a href="products.php" class="btn btn-secondary">Back to Products</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" class="form-control" name="name" id="name" value="<?php echo $product['name'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3"><?php echo $product['description'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" name="category" id="category" value="<?php echo $product['category'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="sku">SKU *</label>
                <input type="text" class="form-control" name="sku" id="sku" value="<?php echo $product['sku'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="purchase_price">Purchase Price *</label>
                <input type="number" step="0.01" class="form-control" name="purchase_price" id="purchase_price" value="<?php echo $product['purchase_price'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="unit_price">Unit Price *</label>
                <input type="number" step="0.01" class="form-control" name="unit_price" id="unit_price" value="<?php echo $product['unit_price'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="selling_price">Selling Price *</label>
                <input type="number" step="0.01" class="form-control" name="selling_price" id="selling_price" value="<?php echo $product['selling_price'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" class="form-control" name="supplier" id="supplier" value="<?php echo $product['supplier'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="status">Stock Status *</label>
                <select class="form-control" name="status" id="status" required>
                    <option value="in_stock" <?php echo ($product['status'] ?? '') === 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                    <option value="low_stock" <?php echo ($product['status'] ?? '') === 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                    <option value="out_of_stock" <?php echo ($product['status'] ?? '') === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update' : 'Add'; ?> Product</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
