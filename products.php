<?php
require_once 'includes/functions.php';
requireLogin();

$success = '';
$error = '';

// Delete product
if (isset($_GET['delete']) && hasRole('manager')) {
    $id = sanitize($_GET['delete']);
    $result = deleteProduct($id);
 
    if ($result === "deleted") {
        $success = "Product deleted successfully.";
    } elseif ($result === "linked") {
        $error = "Cannot delete product: it has related sales records.";
    } else {
        $error = "An unexpected error occurred while deleting the product.";
    }
 }
 

// Get search term if provided
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Get all products (filtered by search if provided)
$products = getAllProductsWithStockStatus($search);

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title"><i class="fas fa-boxes mr-2"></i>Products</h1>
        <p class="page-subtitle">Manage your inventory products</p>
    </div>
    <?php if (hasRole('manager')): ?>
        <a href="product-form.php" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add New Product
        </a>
    <?php endif; ?>
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

<!-- Add search form -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-search mr-2"></i>Search Products</h6>
    </div>
    <div class="card-body">
        <form method="get" action="products.php" class="search-form">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by name, SKU or description..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold"><i class="fas fa-list mr-2"></i>Product List</h6>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-4">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <?php if (!empty($search)): ?>
                    <p class="text-muted">No products found matching "<?php echo htmlspecialchars($search); ?>".</p>
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Show All Products
                    </a>
                <?php else: ?>
                    <p class="text-muted">No products found.</p>
                    <?php if (hasRole('manager')): ?>
                        <a href="product-form.php" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add New Product
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Purchase Price</th>
                            <th>Unit Price</th>
                            <th>Selling Price</th>
                            <th>Stock Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['sku']; ?></td>
                                <td>$<?php echo number_format($product['purchase_price'], 2); ?></td>
                                <td>$<?php echo number_format($product['unit_price'], 2); ?></td>
                                <td>$<?php echo number_format($product['selling_price'], 2); ?></td>
                                <td>
                                    <?php
                                    $stockStatus = $product['status'];
                                    
                                    // Apply the conditions for displaying stock status
                                    if ($stockStatus == 0) {
                                        echo '<span class="bange bange-danger">'. 'Out of Stock'. '</span>';
                                    } elseif ($stockStatus <= 10) {
                                        echo "Low Stock";
                                    } else {
                                        echo '<span class="bange bange-success">'. 'In Stock'. '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info" data-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (hasRole('manager' || 'admin')): ?>
                                            <a href="product-form.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')" data-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (hasRole('user')): ?>
                                            <a href="record-sale.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-success" data-toggle="tooltip" title="Record Sale">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
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
    
    .badge {
        font-size: 0.9rem;
        padding: 0.35rem 0.5rem;
        border-radius: 0.25rem;
    }

    /* Add styles for search form */
    .search-form {
        margin-bottom: 0;
    }

    .search-form .form-control {
        border-radius: 50px 0 0 50px;
        padding-left: 1.5rem;
    }

    .search-form .btn {
        border-radius: 0 50px 50px 0;
        padding-left: 1.2rem;
        padding-right: 1.2rem;
    }

    .search-form .btn-secondary {
        border-radius: 50px;
        margin-left: 0.5rem;
    }

    /* Highlight search results */
    .highlight {
        background-color: #fff3cd;
        padding: 0.1rem 0.2rem;
        border-radius: 3px;
    }

    @media (max-width: 576px) {
        .search-form .input-group {
            flex-direction: column;
        }
        
        .search-form .form-control,
        .search-form .btn {
            border-radius: 50px;
            margin-bottom: 0.5rem;
        }
        
        .search-form .input-group-append {
            display: flex;
            width: 100%;
        }
        
        .search-form .input-group-append .btn {
            flex: 1;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
