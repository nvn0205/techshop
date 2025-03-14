<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Quản lý đơn hàng";
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/admin/orders.php'));
    exit;
}

// Get all orders
$orders = getAllOrders();

// Handle filter and sort
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Apply filters
if ($filter === 'pending') {
    $orders = array_filter($orders, function($order) {
        return $order['status'] === 'pending';
    });
} elseif ($filter === 'processing') {
    $orders = array_filter($orders, function($order) {
        return $order['status'] === 'processing';
    });
} elseif ($filter === 'shipping') {
    $orders = array_filter($orders, function($order) {
        return $order['status'] === 'shipping';
    });
} elseif ($filter === 'completed') {
    $orders = array_filter($orders, function($order) {
        return $order['status'] === 'completed';
    });
} elseif ($filter === 'cancelled') {
    $orders = array_filter($orders, function($order) {
        return $order['status'] === 'cancelled';
    });
}

// Apply sorting
if ($sort === 'newest') {
    usort($orders, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
} elseif ($sort === 'oldest') {
    usort($orders, function($a, $b) {
        return strtotime($a['created_at']) - strtotime($b['created_at']);
    });
} elseif ($sort === 'total_desc') {
    usort($orders, function($a, $b) {
        return $b['total_amount'] - $a['total_amount'];
    });
} elseif ($sort === 'total_asc') {
    usort($orders, function($a, $b) {
        return $a['total_amount'] - $b['total_amount'];
    });
}

// Handle order status update
$message = '';
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $result = updateOrderStatus($order_id, $status);
    $message = $result['message'];

    // Refresh order list
    $orders = getAllOrders();

    // Re-apply filter and sort
    if ($filter) {
        $orders = array_filter($orders, function($order) use ($filter) {
            return $order['status'] === $filter;
        });
    }

    if ($sort) {
        usort($orders, function($a, $b) use ($sort) {
            if ($sort === 'newest') {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            } elseif ($sort === 'oldest') {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            } elseif ($sort === 'total_desc') {
                return $b['total_amount'] - $a['total_amount'];
            } elseif ($sort === 'total_asc') {
                return $a['total_amount'] - $b['total_amount'];
            }
            return 0;
        });
    }
}

// View specific order details
$viewOrderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$viewOrder = null;

if ($viewOrderId > 0) {
    $viewOrder = getOrderById($viewOrderId);
}

require_once 'layout/header.php';
?>



  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Quản lý /</span> Đơn hàng
      </h4>

      <?php if (!empty($message)): ?>
      <div class="alert alert-success alert-dismissible" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <?php if ($viewOrder): ?>
      <!-- Order details view -->
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Chi tiết đơn hàng #<?= $viewOrder['id'] ?></h5>
              <a href="orders.php" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Quay lại
              </a>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <h6>Thông tin khách hàng</h6>
                  <p><strong>Họ tên:</strong> <?= isset($viewOrder['full_name']) && !empty($viewOrder['full_name']) ? htmlspecialchars($viewOrder['full_name']) : (isset($viewOrder['user_full_name']) && !empty($viewOrder['user_full_name']) ? htmlspecialchars($viewOrder['user_full_name']) : 'Khách vãng lai') ?></p>
                  <p><strong>Email:</strong> <?= isset($viewOrder['email']) && !empty($viewOrder['email']) ? htmlspecialchars($viewOrder['email']) : 'N/A' ?></p>
                  <p><strong>Điện thoại:</strong> <?= isset($viewOrder['phone']) && !empty($viewOrder['phone']) ? htmlspecialchars($viewOrder['phone']) : 'N/A' ?></p>
                  <p><strong>Địa chỉ:</strong> <?= isset($viewOrder['shipping_address']) && !empty($viewOrder['shipping_address']) ? htmlspecialchars($viewOrder['shipping_address']) : 'N/A' ?></p>
                </div>
                <div class="col-md-6">
                  <h6>Thông tin đơn hàng</h6>
                  <p><strong>Mã đơn hàng:</strong> #<?= $viewOrder['id'] ?></p>
                  <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($viewOrder['created_at'])) ?></p>
                  <p><strong>Tổng tiền:</strong> <?= formatPrice($viewOrder['total_amount']) ?></p>
                  <form action="" method="post" class="mt-3">
                    <input type="hidden" name="order_id" value="<?= $viewOrder['id'] ?>">
                    <div class="input-group">
                      <select name="status" class="form-select">
                        <option value="pending" <?= $viewOrder['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="processing" <?= $viewOrder['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="shipping" <?= $viewOrder['status'] === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                        <option value="completed" <?= $viewOrder['status'] === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="cancelled" <?= $viewOrder['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                      </select>
                      <button type="submit" name="update_status" class="btn btn-primary">Cập nhật</button>
                    </div>
                  </form>
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
                    <?php foreach ($viewOrder['items'] as $item): ?>
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
                      <td><strong><?= formatPrice($viewOrder['total_amount']) ?></strong></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <!-- Orders list view -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                  <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      <i class="bx bx-filter-alt me-1"></i> Lọc
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item <?= $filter === '' ? 'active' : '' ?>" href="?">Tất cả</a></li>
                      <li><a class="dropdown-item <?= $filter === 'pending' ? 'active' : '' ?>" href="?filter=pending">Chờ xử lý</a></li>
                      <li><a class="dropdown-item <?= $filter === 'processing' ? 'active' : '' ?>" href="?filter=processing">Đang xử lý</a></li>
                      <li><a class="dropdown-item <?= $filter === 'shipping' ? 'active' : '' ?>" href="?filter=shipping">Đang giao hàng</a></li>
                      <li><a class="dropdown-item <?= $filter === 'completed' ? 'active' : '' ?>" href="?filter=completed">Đã hoàn thành</a></li>
                      <li><a class="dropdown-item <?= $filter === 'cancelled' ? 'active' : '' ?>" href="?filter=cancelled">Đã hủy</a></li>
                    </ul>
                  </div>
                  <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      <i class="bx bx-sort me-1"></i> Sắp xếp
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>" href="?sort=newest">Mới nhất</a></li>
                      <li><a class="dropdown-item <?= $sort === 'oldest' ? 'active' : '' ?>" href="?sort=oldest">Cũ nhất</a></li>
                      <li><a class="dropdown-item <?= $sort === 'total_desc' ? 'active' : '' ?>" href="?sort=total_desc">Giá trị cao nhất</a></li>
                      <li><a class="dropdown-item <?= $sort === 'total_asc' ? 'active' : '' ?>" href="?sort=total_asc">Giá trị thấp nhất</a></li>
                    </ul>
                  </div>
                </div>
                <form action="" method="get" class="d-flex gap-2">
                  <input type="text" name="search" class="form-control" placeholder="Tìm kiếm đơn hàng..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                  <button type="submit" class="btn btn-primary">
                    <i class="bx bx-search"></i>
                  </button>
                </form>
              </div>

              <div class="table-responsive">
                <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                  <h4 class="text-muted mb-0">Không có đơn hàng nào</h4>
                </div>
                <?php else: ?>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Khách hàng</th>
                      <th>Tài khoản</th>
                      <th>Thông tin liên hệ</th>
                      <th>Ngày đặt</th>
                      <th>Tổng tiền</th>
                      <th>Trạng thái</th>
                      <th>Hành động</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                      <td><strong>#<?= $order['id'] ?></strong></td>
                      <td><?= isset($order['full_name']) && !empty($order['full_name']) ? htmlspecialchars($order['full_name']) : 'Khách vãng lai' ?></td>
                      <td>
                        <?php if(isset($order['username']) && !empty($order['username'])): ?>
                        <span class="badge bg-label-primary"><?= htmlspecialchars($order['username']) ?></span>
                        <?php else: ?>
                        <span class="badge bg-label-secondary">Khách vãng lai</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div><?= isset($order['email']) && !empty($order['email']) ? htmlspecialchars($order['email']) : 'N/A' ?></div>
                        <div class="text-muted"><?= isset($order['phone']) && !empty($order['phone']) ? htmlspecialchars($order['phone']) : 'N/A' ?></div>
                      </td>
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
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="?id=<?= $order['id'] ?>">
                              <i class="bx bx-show-alt me-1"></i> Xem chi tiết
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="printOrder(<?= $order['id'] ?>)">
                              <i class="bx bx-printer me-1"></i> In đơn hàng
                            </a>
                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteOrder(<?= $order['id'] ?>)">
                              <i class="bx bx-trash me-1"></i> Xóa
                            </a>
                          </div>
                        </div>
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
      <?php endif; ?>
    </div>
    <!-- / Content -->

<?php require_once 'layout/footer.php'; ?>

<script>
function printOrder(orderId) {
    window.open('print-order.php?id=' + orderId, '_blank');
}

function deleteOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
        window.location.href = '?delete=' + orderId;
    }
}
</script>

</div>
</div>REM Update 14 - Manage orders 
REM Update 14 - Manage orders 
