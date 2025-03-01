<?php
$pageTitle = "Tra cứu đơn hàng";
require_once 'config.php';
require_once 'functions.php';

$message = '';
$orders = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_type = $_POST['search_type'] ?? '';
    $search_value = trim($_POST['search_value'] ?? '');
    
    if (empty($search_value)) {
        $message = 'Vui lòng nhập thông tin tra cứu';
    } else {
        if ($search_type === 'order_id') {
            $order = getOrderById($search_value);
            if ($order) {
                $orders = [$order];
            }
        } else if ($search_type === 'phone') {
            $orders = getOrdersByPhone($search_value);
        }
        
        if (empty($orders)) {
            $message = 'Không tìm thấy đơn hàng nào';
        }
    }
}

include 'header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Tra cứu đơn hàng</h1>
                    
                    <form method="post" class="mb-4">
                        <div class="mb-3">
                            <label class="form-label">Chọn cách tra cứu:</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="search_type" id="search_type_order" value="order_id" <?= (!isset($_POST['search_type']) || $_POST['search_type'] === 'order_id') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="search_type_order">
                                    Tra cứu bằng mã đơn hàng
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="search_type" id="search_type_phone" value="phone" <?= (isset($_POST['search_type']) && $_POST['search_type'] === 'phone') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="search_type_phone">
                                    Tra cứu bằng số điện thoại
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="search_value" class="form-label">Nhập thông tin tra cứu:</label>
                            <input type="text" class="form-control" id="search_value" name="search_value" value="<?= htmlspecialchars($_POST['search_value'] ?? '') ?>" required>
                            <div class="form-text" id="search_help"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Tra cứu
                            </button>
                        </div>
                    </form>
                    
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-info">
                        <?= $message ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($orders)): ?>
                    <div class="orders-results">
                        <?php foreach ($orders as $order): ?>
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Đơn hàng #<?= $order['id'] ?></h5>
                                <span class="badge bg-<?= getStatusClass($order['status']) ?>">
                                    <?= getStatusText($order['status']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                                <p><strong>Tổng tiền:</strong> <?= formatPrice($order['total_amount']) ?></p>
                                
                                <h6 class="mt-3">Chi tiết đơn hàng</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Giá</th>
                                                <th>Số lượng</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($item['image'])): ?>
                                                        <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" width="50" height="50" class="me-2">
                                                        <?php endif; ?>
                                                        <?= $item['name'] ?>
                                                    </div>
                                                </td>
                                                <td><?= formatPrice($item['price']) ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchTypeOrderRadio = document.getElementById('search_type_order');
    const searchTypePhoneRadio = document.getElementById('search_type_phone');
    const searchValueInput = document.getElementById('search_value');
    const searchHelp = document.getElementById('search_help');
    
    function updateHelp() {
        if (searchTypeOrderRadio.checked) {
            searchHelp.textContent = 'Nhập mã đơn hàng (ví dụ: 1234)';
            searchValueInput.setAttribute('placeholder', 'Nhập mã đơn hàng...');
        } else {
            searchHelp.textContent = 'Nhập số điện thoại đặt hàng';
            searchValueInput.setAttribute('placeholder', 'Nhập số điện thoại...');
        }
    }
    
    searchTypeOrderRadio.addEventListener('change', updateHelp);
    searchTypePhoneRadio.addEventListener('change', updateHelp);
    
    // Initialize help text
    updateHelp();
});
</script>

<?php include 'footer.php'; ?>
