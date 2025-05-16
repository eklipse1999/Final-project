<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

$conn = connectDb();

// Check login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Get stock ID from query string
if (!isset($_GET['id'])) {
    echo "Invalid stock ID.";
    exit;
}

$stock_id = $_GET['id'];
$stock = getStockById($conn, $stock_id); // You need to create this function

if (!$stock) {
    echo "Stock not found.";
    exit;
}

// Handle form submission
if (isset($_POST['update_stock'])) {
    $new_quantity = $_POST['quantity'];
    updateStock($conn, $stock_id, $new_quantity); // Already defined in your `stock.php`
    header("Location: stock.php");
    exit;
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Stock</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Edit Stock: <?php echo htmlspecialchars($stock['name']); ?></h2>
    <form method="POST">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($stock['name']); ?>" disabled>
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?php echo $stock['quantity']; ?>" required>
        </div>
        <button type="submit" name="update_stock" class="btn btn-primary">Update Stock</button>
        <a href="stock.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
