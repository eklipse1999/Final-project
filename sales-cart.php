<?php
require_once 'includes/functions.php';
requireLogin();

$products = getAllProducts();
$cart = $_SESSION['sales_cart'] ?? [];
$success = '';
$error = '';

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = sanitize($_POST['product_id']);
    $quantity = (int) sanitize($_POST['quantity']);

    $product = getProductById($productId);
    if (!$product) {
        $error = "Product not found.";
    } elseif ($quantity <= 0 || $quantity > $product['quantity']) {
        $error = "Invalid quantity.";
    } else {
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $product['name'],
                'price' => $product['selling_price'],
                'quantity' => $quantity
            ];
        }
        $_SESSION['sales_cart'] = $cart;
        $success = "Product added to cart.";
    }
}

// Handle removing from cart
if (isset($_GET['remove'])) {
    $removeId = sanitize($_GET['remove']);
    unset($cart[$removeId]);
    $_SESSION['sales_cart'] = $cart;
    $success = "Product removed from cart.";
}

// Handle finalizing sale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_sale'])) {
    $userId = $_SESSION['user_id'];
    $errors = [];

    foreach ($cart as $productId => $item) {
        $product = getProductById($productId);
        if (!$product || $item['quantity'] > $product['quantity']) {
            $errors[] = "Insufficient stock for {$item['name']}.";
        }
    }

    if (empty($errors)) {
        foreach ($cart as $productId => $item) {
            recordSale($productId, $item['quantity'], $userId);
        }
        $_SESSION['sales_cart'] = [];
        $cart = [];
        $success = "Sale recorded successfully.";
    } else {
        $error = implode('<br>', $errors);
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Sales Cart</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" class="form-inline mb-4">
        <label class="mr-2" for="product_id">Product:</label>
        <select name="product_id" id="product_id" class="form-control mr-2" required>
            <option value="">Select a product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>">
                    <?php echo $product['name']; ?> (Available: <?php echo $product['quantity']; ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label class="mr-2" for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" class="form-control mr-2" min="1" required>

        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
    </form>

    <?php if (!empty($cart)): ?>
        <h2>Cart Items</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($cart as $productId => $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; ?>
                    <?php $total += $subtotal; ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="sales-cart.php?remove=<?php echo $productId; ?>" class="btn btn-danger btn-sm">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3">Total</th>
                    <th colspan="2">$<?php echo number_format($total, 2); ?></th>
                </tr>
            </tbody>
        </table>

        <form method="post">
            <button class="btn btn-info" onclick="window.print()">Print Report</button>
            <button type="submit" name="finalize_sale" class="btn btn-success">Finalize Sale</button>
        </form>
    <?php else: ?>
        <p>No items in the cart.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
