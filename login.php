<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = sanitize($_POST['company_id']); // New company_id field
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($company_id) || empty($username) || empty($password)) {
        $error = "Please enter company ID, username and password";
    } else {
        $conn = connectDB();
        $company_id = $conn->real_escape_string($company_id);
        $username = $conn->real_escape_string($username);
        
        $conn = connectDB();

// Use prepared statement
$stmt = $conn->prepare("SELECT id, username, company_id, role, password FROM users WHERE company_id = ? AND username = ? LIMIT 1");

$stmt->bind_param("ss", $company_id, $username); 
// "ss" = two strings (company_id, username)

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['company_id'] = $user['company_id'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
} else {
    $error = "Invalid credentials";
}

$stmt->close();
$conn->close();;
    }
}

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card auth-card">
            <div class="card-header">
                <h4 class="auth-title"><i class="fas fa-sign-in-alt"></i> Login</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" class="auth-form">
                    <div class="form-group">
                        <label for="company_id"><i class="fas fa-building mr-2"></i>Company ID</label>
                        <input type="text" class="form-control" id="company_id" name="company_id" required>
                    </div>
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user mr-2"></i>Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock mr-2"></i>Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="register.php" class="auth-link">Register</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-card {
        margin-top: 2rem;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .auth-title {
        margin-bottom: 0;
        color: #5a5c69;
        font-weight: 700;
    }
    
    .auth-form {
        padding: 1rem 0;
    }
    
    .auth-link {
        color: var(--primary-color);
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .auth-link:hover {
        text-decoration: none;
        color: #2e59d9;
    }
</style>

<?php require_once 'includes/footer.php'; ?>
