<?php
require_once 'includes/functions.php';
requireLogin();

// Only Admin or Manager can access
if (!isAdmin() && !isManager()) {
    header('Location: dashboard.php');
    exit;
}

$conn = connectDB();
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($product_id && $quantity > 0) {
        // Check if product exists in the products table
        $product_check = $conn->prepare("SELECT id FROM products WHERE id = ?");
        $product_check->bind_param("i", $product_id);
        $product_check->execute();
        $product_result = $product_check->get_result();

        if ($product_result->num_rows === 0) {
            $message = "Selected product does not exist.";
        } else {
            // Check if stock entry exists
            $check = $conn->prepare("SELECT quantity FROM stock WHERE product_id = ?");
            $check->bind_param("i", $product_id);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                // Update existing stock
                $update = $conn->prepare("UPDATE stock SET quantity = quantity + ?, last_updated = NOW() WHERE product_id = ?");
                $update->bind_param("ii", $quantity, $product_id);
                $update->execute();
                $message = "Stock updated successfully!";
            } else {
                // Insert new stock entry
                $insert = $conn->prepare("INSERT INTO stock (product_id, quantity, last_updated) VALUES (?, ?, NOW())");
                $insert->bind_param("ii", $product_id, $quantity);
                $insert->execute();
                $message = "Stock added successfully!";
            }
        }
    } else {
        $message = "Please select a product and enter a valid quantity.";
    }
}

// Get all available products
$products = $conn->query("SELECT id, name FROM products ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();

require_once 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-plus-circle"></i> Add Stock</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="product_id">Select Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">-- Select --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['product_id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Stock Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-check-circle"></i> Add Stock</button>
        <a href="dashboard.php" class="btn btn-secondary ml-2">Back to Dashboard</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
