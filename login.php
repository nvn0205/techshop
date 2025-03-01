<?php
$pageTitle = "Đăng nhập";
require_once 'config.php';
require_once 'functions.php';

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: ' . $config['site_url']);
    exit;
}

$error = '';
$username = '';

// Handle login form submission
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $user = authenticateUser($username, $password);
        
        if ($user) {
            // Login successful
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to intended page or homepage
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : $config['site_url'];
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng';
        }
    }
}

include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Đăng nhập</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">Đăng nhập</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Chưa có tài khoản? <a href="<?= $config['site_url'] ?>/register.php">Đăng ký ngay</a></p>
                        <p><a href="#">Quên mật khẩu?</a></p>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p>Hoặc đăng nhập với</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-outline-primary">
                                <i class="fab fa-facebook-f me-2"></i> Facebook
                            </button>
                            <button class="btn btn-outline-danger">
                                <i class="fab fa-google me-2"></i> Google
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
REM Update 8 - Fix login issues 
