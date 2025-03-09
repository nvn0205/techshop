<?php
$pageTitle = "Đăng ký tài khoản";
require_once 'config.php';
require_once 'functions.php';

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: ' . $config['site_url']);
    exit;
}

$error = '';
$success = '';
$formData = [
    'username' => '',
    'email' => '',
    'full_name' => ''
];

// Handle registration form submission
if (isset($_POST['register'])) {
    $formData = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'full_name' => trim($_POST['full_name'])
    ];
    
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($formData['username']) || empty($formData['email']) || empty($formData['full_name']) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } else {
        // Attempt to register user
        $result = registerUser($formData['username'], $password, $formData['email'], $formData['full_name']);
        
        if ($result['success']) {
            $success = $result['message'];
            // Clear form data
            $formData = [
                'username' => '',
                'email' => '',
                'full_name' => ''
            ];
        } else {
            $error = $result['message'];
        }
    }
}

include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?= $success ?> <a href="<?= $config['site_url'] ?>/login.php">Đăng nhập ngay</a>
                    </div>
                    <?php endif; ?>
                    
                    <form action="" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($formData['username']) ?>" required>
                                <small class="text-muted">Tên đăng nhập không có dấu cách và ký tự đặc biệt</small>
                            </div>
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($formData['full_name']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                Tôi đồng ý với <a href="#">điều khoản sử dụng</a> và <a href="#">chính sách bảo mật</a>
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="register" class="btn btn-primary">Đăng ký</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Đã có tài khoản? <a href="<?= $config['site_url'] ?>/login.php">Đăng nhập</a></p>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <p>Hoặc đăng ký với</p>
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
REM Update 9 - Add registration validation 
REM Update 9 - Add registration validation 
