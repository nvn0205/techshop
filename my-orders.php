<?php
$pageTitle = "Đơn hàng của tôi";
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/my-orders.php'));
    exit;
}

// Get user's orders
$orders = getUserOrders($_SESSION['user_id']);

include 'header.php';
?>

<div class="container py-4">
    <h1 class="mb-4">Đơn hàng của tôi</h1>
    
    <?php if (empty($orders)): ?>
    <div class="alert alert-info">
        Bạn chưa có đơn hàng nào.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                    <td><?= formatPrice($order['total_amount']) ?></td>
                    <td>
                        <span class="badge bg-<?= getStatusClass($order['status']) ?>">
                            <?= getStatusText($order['status']) ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id'] ?>">
                            <i class="fas fa-eye"></i> Xem
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Order Details Modals -->
    <?php foreach ($orders as $order): ?>
    <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1" aria-labelledby="orderModalLabel<?= $order['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel<?= $order['id'] ?>">Chi tiết đơn hàng #<?= $order['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Thông tin đơn hàng</h6>
                            <p><strong>Mã đơn hàng:</strong> #<?= $order['id'] ?></p>
                            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            <p><strong>Tổng tiền:</strong> <?= formatPrice($order['total_amount']) ?></p>
                            <p>
                                <strong>Trạng thái:</strong>
                                <span class="badge bg-<?= getStatusClass($order['status']) ?>">
                                    <?= getStatusText($order['status']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin giao hàng</h6>
                            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                            <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                        </div>
                    </div>

                    <h6>Sản phẩm đã đặt</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
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
                                            <img src="<?= $item['image'] ?>" width="50" height="50" class="me-2" alt="<?= $item['name'] ?>">
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
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
