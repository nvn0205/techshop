<?php
$pageTitle = "Giỏ hàng";
require_once 'config.php';
require_once 'functions.php';

// Initialize cart
initCart();

// Handle cart updates
$message = '';
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $result = updateCartItem($product_id, intval($quantity));
    }
    $message = 'Giỏ hàng đã được cập nhật';
}

// Handle remove item
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    $result = removeCartItem($product_id);
    $message = $result['message'];
}

// Handle clear cart
if (isset($_GET['clear'])) {
    clearCart();
    $message = 'Giỏ hàng đã được xóa';
}

include 'header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $config['site_url'] ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Giỏ hàng của bạn</h1>
    
    <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
    <div class="alert alert-info">
        Giỏ hàng của bạn đang trống. <a href="<?= $config['site_url'] ?>">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    
    <form action="" method="post">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-center">Tổng</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($_SESSION['cart'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="cart-product-image me-3">
                                                <h6 class="mb-0"><?= $item['name'] ?></h6>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= formatPrice($item['price']) ?></td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm quantity-control">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="decreaseQuantity(<?= $item['id'] ?>)">-</button>
                                                <input type="number" name="quantity[<?= $item['id'] ?>]" id="quantity_<?= $item['id'] ?>" class="form-control text-center" value="<?= $item['quantity'] ?>" min="1">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="increaseQuantity(<?= $item['id'] ?>)">+</button>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                                        <td class="text-center">
                                            <a href="?remove=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?= $config['site_url'] ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                            </a>
                            <div>
                                <a href="?clear=1" class="btn btn-outline-danger me-2" onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm?');">
                                    <i class="fas fa-trash me-2"></i> Xóa giỏ hàng
                                </a>
                                <button type="submit" name="update_cart" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt me-2"></i> Cập nhật giỏ hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Tổng giỏ hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span><?= formatPrice(getCartTotal()) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-primary"><?= formatPrice(getCartTotal()) ?></strong>
                        </div>
                        
                        <a href="<?= $config['site_url'] ?>/checkout.php" class="btn btn-primary w-100">
                            <i class="fas fa-credit-card me-2"></i> Tiến hành thanh toán
                        </a>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <img src="https://cdn.tgdd.vn/mwgcart/mwgcore/ContentMwg/images/logo-visa.png" alt="Visa" height="30" class="me-2 mb-2">
                            <img src="https://cdn.tgdd.vn/mwgcart/mwgcore/ContentMwg/images/logo-master.png" alt="MasterCard" height="30" class="me-2 mb-2">
                            <img src="https://cdn.tgdd.vn/mwgcart/mwgcore/ContentMwg/images/logo-jcb.png" alt="JCB" height="30" class="me-2 mb-2">
                            <img src="https://cdn.tgdd.vn/mwgcart/mwgcore/ContentMwg/images/logo-atm.png" alt="ATM" height="30" class="me-2 mb-2">
                            <img src="https://cdn.tgdd.vn/mwgcart/mwgcore/ContentMwg/images/logo-momo.png" alt="MoMo" height="30" class="mb-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
function decreaseQuantity(productId) {
    const quantityInput = document.getElementById('quantity_' + productId);
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

function increaseQuantity(productId) {
    const quantityInput = document.getElementById('quantity_' + productId);
    const currentValue = parseInt(quantityInput.value);
    quantityInput.value = currentValue + 1;
}
</script>

<?php include 'footer.php'; ?>
