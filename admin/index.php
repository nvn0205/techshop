<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Quản trị";
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/admin/'));
    exit;
}

// Get statistics
$products = getAllProducts();
$activeProducts = count(array_filter($products, function($product) {
    return $product['status'] === 'active';
}));

$orders = getAllOrders();
$pendingOrders = count(array_filter($orders, function($order) {
    return $order['status'] === 'pending';
}));

// Get users from database
$users = getAllUsers();
$customerCount = count(array_filter($users, function($user) {
    return $user['role'] === 'customer';
}));

// Calculate total revenue
$totalRevenue = 0;
foreach ($orders as $order) {
    if ($order['status'] !== 'cancelled') {
        $totalRevenue += $order['total_amount'];
    }
}

// Get recent orders
$recentOrders = array_slice($orders, 0, 5);

// Sort by date (newest first)
usort($recentOrders, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Get low stock products
$lowStockProducts = array_filter($products, function($product) {
    return $product['stock'] <= 10 && $product['status'] === 'active';
});
usort($lowStockProducts, function($a, $b) {
    return $a['stock'] - $b['stock'];
});
$lowStockProducts = array_slice($lowStockProducts, 0, 5);

require_once 'layout/header.php';
?>



  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
        <div class="col-lg-8 mb-4 order-0">
          <div class="card">
            <div class="d-flex align-items-end row">
              <div class="col-sm-7">
                <div class="card-body">
                  <h5 class="card-title text-primary">Xin chào <?= $_SESSION['user']['full_name'] ?>! 🎉</h5>
                  <p class="mb-4">
                    Hôm nay bạn có <span class="fw-bold"><?= $pendingOrders ?></span> đơn hàng mới cần xử lý
                  </p>

                  <a href="orders.php" class="btn btn-sm btn-outline-primary">Xem đơn hàng</a>
                </div>
              </div>
              <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-4">
                  <img src="<?= $config['site_url'] ?>/admin/assets/img/illustrations/man-with-laptop.png" height="140" alt="View Badge User" data-app-dark-img="<?= $config['site_url'] ?>/admin/assets/illustrations/man-with-laptop-dark.png" data-app-light-img="<?= $config['site_url'] ?>/admin/assets/illustrations/man-with-laptop-light.png" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-4 col-md-4 order-1">
          <div class="row">
            <div class="col-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                      <img src="<?= $config['site_url'] ?>/admin/assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded" />
                    </div>
                  </div>
                  <span class="d-block mb-1">Doanh thu</span>
                  <h3 class="card-title text-nowrap mb-2"><?= formatPrice($totalRevenue) ?></h3>
                  <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Tổng doanh thu</small>
                </div>
              </div>
            </div>
            <div class="col-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                      <img src="<?= $config['site_url'] ?>/admin/assets/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded" />
                    </div>
                  </div>
                  <span class="fw-semibold d-block mb-1">Đơn hàng mới</span>
                  <h3 class="card-title mb-2"><?= $pendingOrders ?></h3>
                  <small class="text-success fw-semibold">Chờ xử lý</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="col-12 order-2 mb-4">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0">Đơn hàng gần đây</h5>
              <a href="orders.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
              <div class="table-responsive text-nowrap">
                <?php if (empty($recentOrders)): ?>
                <p class="text-muted mb-0">Chưa có đơn hàng nào.</p>
                <?php else: ?>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Mã đơn hàng</th>
                      <th>Khách hàng</th>
                      <th>Ngày đặt</th>
                      <th>Tổng tiền</th>
                      <th>Trạng thái</th>
                      <th>Hành động</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                      <td><strong>#<?= $order['id'] ?></strong></td>
                      <td><?= isset($order['full_name']) && !empty($order['full_name']) ? htmlspecialchars($order['full_name']) : 'Khách vãng lai' ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                      <td><?= formatPrice($order['total_amount']) ?></td>
                      <td>
                        <?php
                        switch ($order['status']) {
                            case 'pending':
                                echo '<span class="badge bg-label-warning">Chờ xử lý</span>';
                                break;
                            case 'processing':
                                echo '<span class="badge bg-label-info">Đang xử lý</span>';
                                break;
                            case 'shipping':
                                echo '<span class="badge bg-label-primary">Đang giao</span>';
                                break;
                            case 'completed':
                                echo '<span class="badge bg-label-success">Hoàn thành</span>';
                                break;
                            case 'cancelled':
                                echo '<span class="badge bg-label-danger">Đã hủy</span>';
                                break;
                            default:
                                echo '<span class="badge bg-label-secondary">Không xác định</span>';
                        }
                        ?>
                      </td>
                      <td>
                        <a href="orders.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-icon btn-outline-primary">
                          <i class="bx bx-show"></i>
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Low Stock Products Table -->
        <div class="col-12 order-3">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title m-0">Sản phẩm sắp hết hàng</h5>
              <a href="products.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
              <div class="table-responsive text-nowrap">
                <?php if (empty($lowStockProducts)): ?>
                <p class="text-muted mb-0">Không có sản phẩm nào sắp hết hàng.</p>
                <?php else: ?>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Sản phẩm</th>
                      <th>SKU</th>
                      <th>Giá</th>
                      <th>Tồn kho</th>
                      <th>Hành động</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <?php foreach ($lowStockProducts as $product): ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="<?= $product['images'][0] ?>" alt="<?= $product['name'] ?>" class="me-2" width="40" height="40">
                          <strong><?= $product['name'] ?></strong>
                        </div>
                      </td>
                      <td><?= $product['sku'] ?></td>
                      <td><?= formatPrice($product['price']) ?></td>
                      <td><span class="text-danger fw-bold"><?= $product['stock'] ?></span></td>
                      <td>
                        <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-icon btn-outline-primary">
                          <i class="bx bx-edit"></i>
                        </a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->

<?php require_once 'layout/footer.php'; ?>
