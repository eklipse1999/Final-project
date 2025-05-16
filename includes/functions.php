<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Create a global connection variable
$mysqli = connectDB();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isManager() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'manager';
}


function generateBTCTUserID(PDO $pdo): string {
    $prefix = 'BTCT';
    $year = date('Y');
    
    // Fetch the last inserted user_id (if any)
    $stmt = $pdo->query("SELECT user_id FROM users WHERE user_id LIKE 'BTCT-$year-%' ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($last) {
        // Extract the numeric part and increment
        $lastNumber = (int)substr($last['user_id'], -5);
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '00001';
    }

    return "$prefix-$year-$newNumber";
}

// Check user role
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['role'] == $role || 
           ($_SESSION['role'] == 'admin' && ($role == 'manager' || $role == 'user')) ||
           ($_SESSION['role'] == 'manager' && $role == 'user');
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Redirect if not authorized
function requireRole($role) {
    requireLogin();
    
    if (!hasRole($role)) {
        header("Location: unauthorized.php");
        exit;
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Get user by ID
function getUserById($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $conn->close();
        return $user;
    }
    
    $conn->close();
    return null;
}

// Get all products
function getAllProducts($search = '') {
    $conn = connectDB();
    
    $sql = "SELECT p.*, s.quantity 
            FROM products p 
            LEFT JOIN stock s ON p.id = s.product_id";
    
     // Add soft delete filter
    //  $sql .= " WHERE p.is_deleted = 0";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (p.name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.description LIKE '%$search%')";
    }
    
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $conn->close();
    return $products;
}

// Get product by ID
function getProductById($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT p.*, s.quantity 
            FROM products p 
            LEFT JOIN stock s ON p.id = s.product_id 
            WHERE p.id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $conn->close();
        return $product;
    }
    
    $conn->close();
    return null;
}
// Add product
function addProduct($name, $description, $category, $sku, $purchase_price, $unit_price, $selling_price, $supplier, $status) {
    $conn = connectDB();
    
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $category = $conn->real_escape_string($category);
    $sku = $conn->real_escape_string($sku);
    $purchase_price = $conn->real_escape_string($purchase_price);
    $unit_price = $conn->real_escape_string($unit_price);
    $selling_price = $conn->real_escape_string($selling_price);
    $supplier = $conn->real_escape_string($supplier);
    $status = $conn->real_escape_string($status);
    $date_added = date("Y-m-d H:i:s");
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert product
        $sql = "INSERT INTO products (name, description, category, sku, purchase_price, unit_price, selling_price, supplier, date_added, status) 
                VALUES ('$name', '$description', '$category', '$sku', '$purchase_price', '$unit_price', '$selling_price', '$supplier', '$date_added', '$status')";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error adding product: " . $conn->error);
        }
        
        $product_id = $conn->insert_id;
        
        // Insert stock with 0 quantity as default
        $sql = "INSERT INTO stock (product_id, quantity) VALUES ('$product_id', 0)";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error adding stock: " . $conn->error);
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        echo $e->getMessage();
        return false;
    }
}


// Update product
function updateProduct($id, $name, $description, $category, $sku, $purchase_price, $unit_price, $selling_price, $supplier, $status) {
    $conn = connectDB();
    
    $id = $conn->real_escape_string($id);
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $category = $conn->real_escape_string($category);
    $sku = $conn->real_escape_string($sku);
    $purchase_price = $conn->real_escape_string($purchase_price);
    $unit_price = $conn->real_escape_string($unit_price);
    $selling_price = $conn->real_escape_string($selling_price);
    $supplier = $conn->real_escape_string($supplier);
    $status = $conn->real_escape_string($status);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $sql = "UPDATE products 
                SET name = '$name',
                    description = '$description',
                    category = '$category',
                    sku = '$sku',
                    purchase_price = '$purchase_price',
                    unit_price = '$unit_price',
                    selling_price = '$selling_price',
                    supplier = '$supplier',
                    status = '$status'
                WHERE products.id = '$id'";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error updating product: " . $conn->error);
        }
        
        // Ensure stock exists (if not, insert with 0 quantity)
        $checkStock = $conn->query("SELECT stock_id FROM stock WHERE product_id = '$id'");
        if ($checkStock->num_rows == 0) {
            $sql = "INSERT INTO stock (product_id, quantity) VALUES ('$id', 0)";
            if ($conn->query($sql) === FALSE) {
                throw new Exception("Error inserting stock: " . $conn->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        echo $e->getMessage();
        return false;
    }
}

function getStockById($conn, $id) {
    $stmt = $conn->prepare("SELECT stock.*, products.name FROM stock 
                            JOIN products ON stock.product_id = products.id 
                            WHERE stock.stock_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function getAllProductsWithStockStatus($search = '') {
    global $mysqli;  // Use the global $mysqli variable

    // Check if the connection is valid
    if ($mysqli === null) {
        die("Database connection is not available.");
    }

    // Prepare the SQL query
    $query = "SELECT p.id, p.name, p.sku, p.purchase_price,p.unit_price,p.selling_price, s.quantity AS status
              FROM products p
              LEFT JOIN stock s ON p.id = s.product_id";

    // Add search condition if provided
    if (!empty($search)) {
        $query .= " WHERE p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?";
    }

    // Prepare the statement and check for errors
    if ($stmt = $mysqli->prepare($query)) {
        // Bind parameters if there's a search term
        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";
            $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
        }

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch all products and return them
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        // Close the statement
        $stmt->close();

        return $products;
    } else {
        // If the statement preparation failed
        die("Error preparing the query: " . $mysqli->error);
    }
}

// Delete product
function deleteProduct($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);

    // Check if sales exist
    $check = $conn->query("SELECT 1 FROM sales WHERE product_id = '$id' LIMIT 1");
    if ($check->num_rows > 0) {
        $conn->close();
        return "linked"; // Prevent deletion
    }

    $sql = "DELETE FROM products WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return "deleted";
    }

    $conn->close();
    return "error";
}

// Record sale
function recordSale($product_id, $quantity) {
    $conn = connectDB();
    
    $product_id = $conn->real_escape_string($product_id);
    $quantity = $conn->real_escape_string($quantity);
    
    // Get product price
    $sql = "SELECT selling_price FROM products WHERE id = '$product_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        $conn->close();
        return false;
    }
    
    $product = $result->fetch_assoc();
    $total_price = $product['selling_price'] * $quantity;

    // Generate transaction ID
    $transaction_id = uniqid('TXN');

    // Get logged in user ID
    $user_id = $_SESSION['user_id'] ?? 0;

    // Get current date
    $sale_date = date('Y-m-d H:i:s');
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert sale with all required fields
        $sql = "INSERT INTO sales (transaction_id, product_id, user_id, quantity, total_price, sale_date)
                VALUES ('$transaction_id', '$product_id', '$user_id', '$quantity', '$total_price', '$sale_date')";
        
        if ($conn->query($sql) === FALSE) {
            throw new Exception("Error recording sale: " . $conn->error);
        }
        
        // Update stock
        $sql = "UPDATE stock 
                SET quantity = quantity - '$quantity' 
                WHERE product_id = '$product_id' AND quantity >= '$quantity'";
        
        if ($conn->query($sql) === FALSE || $conn->affected_rows == 0) {
            throw new Exception("Error updating stock or insufficient quantity");
        }
        
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        echo $e->getMessage();
        return false;
    }
}

// Function to record multiple sales
function recordMultipleSales($productSales, $conn)
{
    $transaction_id = 'TXN_' . date('YmdHis') . '_' . strtoupper(substr(md5(uniqid()), 0, 5));
    $stmt = $conn->prepare("INSERT INTO sales (transaction_id, product_id, quantity, total_price, sale_date) VALUES (?, ?, ?, ?, NOW())");

    foreach ($productSales as $sale) {
        $stmt->bind_param("siid", $transaction_id, $sale['product_id'], $sale['quantity'], $sale['total_price']);
        $stmt->execute();

        // Update stock
        $updateStmt = $conn->prepare("UPDATE stock SET quantity = quantity - ? WHERE product_id = ?");
        $updateStmt->bind_param("ii", $sale['quantity'], $sale['product_id']);
        $updateStmt->execute();
    }

    $stmt->close();
}

function getGroupedSalesReport($conn, $filterDate = null) {
    $sql = "
        SELECT 
            s.transaction_id, 
            s.sale_date, 
            s.quantity,
            s.total_price, 
            p.name AS product_name,
            u.username
        FROM sales s
        JOIN products p ON s.product_id = p.id
        JOIN users u ON s.user_id = u.id
    ";

    if ($filterDate) {
        $sql .= " WHERE DATE(s.sale_date) = '" . $conn->real_escape_string($filterDate) . "'";
    }

    $sql .= " ORDER BY s.sale_date DESC";

    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transaction_id = $row['transaction_id'];

            if (!isset($transactions[$transaction_id])) {
                $transactions[$transaction_id] = [
                    'transaction_id' => $transaction_id,
                    'sale_date' => $row['sale_date'],
                    'products' => [],
                    'username' => $row['username'],
                    'total' => 0,
                ];
            }

            $transactions[$transaction_id]['products'][] = [
                'name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'total_price' => $row['total_price'],
                'sale_date' => $row['sale_date'],
                'username' => $row['username'],
            ];

            $transactions[$transaction_id]['total'] += $row['total_price'];
        }
        return $transactions;
    }
    return [];
}

// Get low stock products
function getLowStockProducts($threshold = 10) {
    $conn = connectDB();
    $threshold = $conn->real_escape_string($threshold);
    
    $sql = "SELECT p.*, s.quantity 
            FROM products p 
            JOIN stock s ON p.id = s.product_id 
            WHERE s.quantity <= '$threshold'";
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $conn->close();
    return $products;
}

// Get sales report
function getSalesReport($start_date = null, $end_date = null) {
    $conn = connectDB();
    
    $sql = "SELECT s.id, p.name, s.quantity, s.total_price, s.sale_date, u.username 
            FROM sales s 
            JOIN products p ON s.product_id = p.id 
            JOIN users u ON s.user_id = u.id";
    
    if ($start_date && $end_date) {
        $start_date = $conn->real_escape_string($start_date);
        $end_date = $conn->real_escape_string($end_date);
        $sql .= " WHERE s.sale_date BETWEEN '$start_date' AND '$end_date'";
    }
    
    $sql .= " ORDER BY s.sale_date DESC";
    
    $result = $conn->query($sql);
    
    $sales = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sales[] = $row;
        }
    }
    
    $conn->close();
    return $sales;
}

// Get all users
function getAllUsers() {
    $conn = connectDB();
    
    $sql = "SELECT id, username, email, role, created_at FROM users";
    $result = $conn->query($sql);
    
    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    $conn->close();
    return $users;
}

// Update user role
function updateUserRole($user_id, $role) {
    $conn = connectDB();
    
    // Validate inputs
    $user_id = (int)$user_id;
    if ($user_id <= 0) {
        return false;
    }
    
    // Validate role
    $valid_roles = ['admin', 'manager', 'user'];
    if (!in_array($role, $valid_roles)) {
        return false;
    }
    
    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $user_id);
    
    $result = $stmt->execute();
    $affected = $stmt->affected_rows;
    
    $stmt->close();
    $conn->close();
    
    return ($result && $affected > 0);
}

// Delete user
function deleteUser($user_id) {
    $conn = connectDB();
    
    // Validate input
    $user_id = (int)$user_id;
    if ($user_id <= 0) {
        return false;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First check if this user has any sales records
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM sales WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        // If user has sales records, update them to admin user (ID 1) instead of deleting
        if ($row['count'] > 0) {
            $admin_id = 1; // Assuming admin has ID 1
            $stmt = $conn->prepare("UPDATE sales SET user_id = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $admin_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        
        // Now delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        if (!$result || $affected == 0) {
            throw new Exception("Failed to delete user");
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->close();
        
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}


function addStock($conn, $product_id, $quantity) {
    // Check if stock entry already exists
    $check = $conn->prepare("SELECT quantity FROM stock WHERE product_id = ?");
    $check->bind_param("i", $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing stock
        $update = $conn->prepare("UPDATE stock SET quantity = quantity + ?, last_updated = NOW() WHERE product_id = ?");
        $update->bind_param("ii", $quantity, $product_id);
        $update->execute();
        $update->close();
    } else {
        // Insert new stock entry
        $insert = $conn->prepare("INSERT INTO stock (product_id, quantity, last_updated) VALUES (?, ?, NOW())");
        $insert->bind_param("ii", $product_id, $quantity);
        $insert->execute();
        $insert->close();
    }

    $check->close();
}


function updateStock($conn, $id, $quantity) {
    $sql = "UPDATE stock 
            SET quantity = '$quantity', last_updated = NOW()
            WHERE stock.stock_id = '$id'";
    $conn->query($sql);
}

function deleteStock($conn, $id) {
    $sql = "DELETE FROM stock WHERE stock.stock_id = '$id'";
    $conn->query($sql);
}

function getAllStocks($conn) {
    $sql = "SELECT stock.stock_id, stock.quantity, stock.last_updated, products.name, 
                   products.purchase_price, products.unit_price, products.selling_price 
            FROM stock 
            JOIN products ON stock.product_id = products.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

//Cancel sales instead of deleting
function cancelSale($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);

    $sql = "UPDATE sales SET is_cancelled = 1 WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return true;
    }

    $conn->close();
    return false;
}

function restoreSale($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE sales SET is_cancelled = 0 WHERE id = ?");
    return $stmt->execute([$id]);
}