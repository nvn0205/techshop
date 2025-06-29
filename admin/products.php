<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Quản lý sản phẩm";
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/admin/products.php'));
    exit;
}

// Get all categories
$categories = getAllCategories();

// Handle filter and sort
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Get all products
$products = getAllProducts();

// Apply filters
if ($filter === 'active') {
    $products = array_filter($products, function($product) {
        return $product['status'] === 'active';
    });
} elseif ($filter === 'inactive') {
    $products = array_filter($products, function($product) {
        return $product['status'] === 'inactive';
    });
} elseif ($filter === 'low_stock') {
    $products = array_filter($products, function($product) {
        return $product['stock'] <= 10;
    });
}

// Handle product delete
$message = '';
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = deleteProduct($id);
    $message = $result['message'];

    // Refresh product list
    $products = getAllProducts();
}

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $products = array_filter($products, function($product) use ($search) {
        return (stripos($product['name'], $search) !== false || 
                stripos($product['sku'], $search) !== false || 
                stripos($product['description'], $search) !== false);
    });
}

require_once 'layout/header.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản lý /</span> Sản phẩm
    </h4>

    <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <?= $message ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Products List -->
    <div class="card">
      <div class="card-header border-bottom">
        <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
          <div class="col-md-4">
            <div class="dropdown">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php
                switch ($filter) {
                    case 'active':
                        echo 'Đang hoạt động';
                        break;
                    case 'inactive':
                        echo 'Không hoạt động';
                        break;
                    case 'low_stock':
                        echo 'Sắp hết hàng';
                        break;
                    default:
                        echo 'Tất cả sản phẩm';
                }
                ?>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= $filter === '' ? 'active' : '' ?>" href="?">Tất cả sản phẩm</a></li>
                <li><a class="dropdown-item <?= $filter === 'active' ? 'active' : '' ?>" href="?filter=active">Đang hoạt động</a></li>
                <li><a class="dropdown-item <?= $filter === 'inactive' ? 'active' : '' ?>" href="?filter=inactive">Không hoạt động</a></li>
                <li><a class="dropdown-item <?= $filter === 'low_stock' ? 'active' : '' ?>" href="?filter=low_stock">Sắp hết hàng</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md-4">
            <form action="" method="get" class="d-flex">
              <input type="text" name="search" class="form-control me-2" placeholder="Tìm kiếm sản phẩm..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-search"></i>
              </button>
            </form>
          </div>
          <div class="col-md-4 text-md-end">
            <a href="add-product.php" class="btn btn-primary">
              <i class="bx bx-plus me-1"></i> Thêm sản phẩm
            </a>
          </div>
        </div>
      </div>

      <div class="card-datatable table-responsive">
        <?php if (empty($products)): ?>
        <div class="text-center py-5">
          <div class="mb-3">
            <i class="bx bx-store-alt bx-lg text-primary"></i>
          </div>
          <h5>Không tìm thấy sản phẩm nào</h5>
          <p class="text-muted">Hãy thêm sản phẩm mới hoặc thử tìm kiếm với từ khóa khác</p>
        </div>
        <?php else: ?>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Hình ảnh</th>
              <th>Sản phẩm</th>
              <th>Danh mục</th>
              <th>Giá</th>
              <th>Giá KM</th>
              <th>Tồn kho</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php foreach ($products as $product): ?>
            <tr>
              <td><?= $product['id'] ?></td>
              <td>
                <div class="avatar avatar-lg me-2">
                  <img src="<?= $product['images'][0] ?>" alt="<?= $product['name'] ?>" class="rounded-3">
                </div>
              </td>
              <td>
                <div class="d-flex flex-column">
                  <strong><?= $product['name'] ?></strong>
                  <small class="text-muted">SKU: <?= $product['sku'] ?></small>
                </div>
              </td>
              <td>
                <?php
                $category = getCategoryById($product['category_id']);
                echo $category ? $category['name'] : 'Không xác định';
                ?>
              </td>
              <td>
                <span class="fw-semibold"><?= formatPrice($product['price']) ?></span>
              </td>
              <td>
                <?php if (isset($product['sale_price']) && $product['sale_price'] > 0): ?>
                <span class="text-danger fw-semibold"><?= formatPrice($product['sale_price']) ?></span>
                <?php else: ?>
                <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-label-<?= $product['stock'] <= 10 ? 'danger' : 'success' ?>"><?= $product['stock'] ?></span>
              </td>
              <td>
                <span class="badge bg-label-<?= $product['status'] === 'active' ? 'success' : 'danger' ?>">
                  <?= $product['status'] === 'active' ? 'Hoạt động' : 'Không hoạt động' ?>
                </span>
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="edit-product.php?id=<?= $product['id'] ?>">
                      <i class="bx bx-edit-alt me-1"></i> Sửa
                    </a>
                    <a class="dropdown-item text-danger" href="javascript:void(0);" 
                       onclick="if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) window.location.href='products.php?delete=<?= $product['id'] ?>'">
                      <i class="bx bx-trash me-1"></i> Xóa
                    </a>
                    <a class="dropdown-item" href="../product.php?id=<?= $product['id'] ?>" target="_blank">
                      <i class="bx bx-show-alt me-1"></i> Xem
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

<?php require_once 'layout/footer.php'; ?>
