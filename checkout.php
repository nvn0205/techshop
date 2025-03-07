<?php
$pageTitle = "Thanh toán";
require_once 'config.php';
require_once 'functions.php';

// Initialize cart
initCart();

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: ' . $config['site_url'] . '/cart.php');
    exit;
}

// Handle checkout submission
$message = '';
$orderCompleted = false;
$orderId = 0;

if (isset($_POST['checkout'])) {
    // Validate checkout data
    $errors = [];
    
    if (empty($_POST['name'])) {
        $errors[] = 'Vui lòng nhập họ tên';
    }
    
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    
    if (empty($_POST['phone'])) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    }
    
    if (empty($_POST['address'])) {
        $errors[] = 'Vui lòng nhập địa chỉ';
    }
    
    if (empty($errors)) {
        $userData = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'] . ', ' . $_POST['district'] . ', ' . $_POST['city'],
            'note' => $_POST['note'] ?? '',
            'payment_method' => $_POST['payment_method'] ?? 'COD'
        ];
        
        $result = createOrder($userData, $_SESSION['cart'], getCartTotal());
        
        if ($result['success']) {
            $orderCompleted = true;
            $orderId = $result['order_id'];
            clearCart();
        } else {
            $message = $result['message'];
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

include 'header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $config['site_url'] ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= $config['site_url'] ?>/cart.php">Giỏ hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Thanh toán</h1>
    
    <?php if (!empty($message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if ($orderCompleted): ?>
    <div class="card mb-4">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
            <h2 class="mb-4">Đặt hàng thành công!</h2>
            <p class="mb-4">Cảm ơn bạn đã đặt hàng tại ShopDunk. Mã đơn hàng của bạn là: <strong>#<?= $orderId ?></strong></p>
            <p class="mb-4">Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
            <a href="<?= $config['site_url'] ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    </div>
    <?php else: ?>
    
    <form action="" method="post">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <select class="form-select" id="city" name="city" required>
                                    <option value="">Chọn tỉnh/thành phố</option>
                                    <option value="Hà Nội">Hà Nội</option>
                                    <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                    <option value="Đà Nẵng">Đà Nẵng</option>
                                    <option value="Hải Phòng">Hải Phòng</option>
                                    <option value="Cần Thơ">Cần Thơ</option>
                                    <!-- Add more cities as needed -->
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="district" name="district" required value="<?= isset($_POST['district']) ? htmlspecialchars($_POST['district']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" required value="<?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú đơn hàng</label>
                            <textarea class="form-control" id="note" name="note" rows="3"><?= isset($_POST['note']) ? htmlspecialchars($_POST['note']) : '' ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment1" value="cod" checked>
                            <label class="form-check-label" for="payment1">
                                <i class="fas fa-money-bill-wave me-2"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment2" value="bank_transfer">
                            <label class="form-check-label" for="payment2">
                                <i class="fas fa-university me-2"></i> Chuyển khoản ngân hàng
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment3" value="credit_card">
                            <label class="form-check-label" for="payment3">
                                <i class="fas fa-credit-card me-2"></i> Thẻ tín dụng/Ghi nợ
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment4" value="momo">
                            <label class="form-check-label" for="payment4">
                                <i class="fas fa-wallet me-2"></i> Ví điện tử (MoMo, ZaloPay)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Đơn hàng của bạn</h5>
                    </div>
                    <div class="card-body">
                        <div class="order-summary">
                            <?php foreach($_SESSION['cart'] as $item): ?>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h6 class="mb-0"><?= $item['name'] ?></h6>
                                    <small class="text-muted">Số lượng: <?= $item['quantity'] ?></small>
                                </div>
                                <span><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span><?= formatPrice(getCartTotal()) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span>Miễn phí</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-primary"><?= formatPrice(getCartTotal()) ?></strong>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    Tôi đã đọc và đồng ý với <a href="#" target="_blank">điều khoản dịch vụ</a>
                                </label>
                            </div>
                            
                            <button type="submit" name="checkout" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check-circle me-2"></i> Đặt hàng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
REM Update 7 - Update checkout page 
REM Update 7 - Update checkout page 
