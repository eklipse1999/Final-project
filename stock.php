<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

requireLogin();
requireRole('admin' || 'manager');
//requireRole('manager');

$conn = connectDb();

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['add_stock'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    addStock($conn, $product_id, $quantity);
}

if (isset($_POST['update_stock'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    updateStock($conn, $id, $quantity);
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    deleteStock($conn, $id);
}

$stocks = getAllStocks($conn);

require_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4 text-center">Manage Stock</h1>

    <!-- Add Stock Form -->
    <form method="POST" action="stock.php" class="mb-4">
        <div class="form-row">
            <div class="form-group col-12 col-md-5">
                <select name="product_id" class="form-control" required>
                    <option value="">Select Product</option>
                    <?php
                    $products = getAllProducts();
                    foreach ($products as $product):
                        ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-12 col-md-4">
                <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
            </div>
            <div class="form-group col-12 col-md-3">
                <button type="submit" name="add_stock" class="btn btn-primary btn-block">
                    <i class="fas fa-plus"></i> Add Stock
                </button>
            </div>
        </div>
    </form>

    <!-- Stock Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Purchase Price</th>
                <th>Unit Price</th>
                <th>Selling Price</th>
                <th>Profit</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stocks as $stock): ?>
                <tr>
                    <td><?php echo htmlspecialchars($stock['name']); ?></td>
                    <td><?php echo $stock['quantity']; ?></td>
                    <td><?php echo number_format($stock['purchase_price'], 2); ?></td>
                    <td><?php echo number_format($stock['unit_price'], 2); ?></td>
                    <td><?php echo number_format($stock['selling_price'], 2); ?></td>
                    <td>
                        <?php 
                        $profit = ($stock['selling_price'] - $stock['purchase_price']) * $stock['quantity'];
                        echo number_format($profit, 2);
                        ?>
                    </td>
                    <td><?php echo $stock['last_updated']; ?></td>
                    <td>
                        <a href="edit_stock.php?id=<?php echo $stock['stock_id']; ?>" class="btn btn-warning btn-sm mb-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="stock.php?delete_id=<?php echo $stock['stock_id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this stock?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
