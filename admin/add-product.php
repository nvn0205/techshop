<?php
$pageTitle = "Thêm sản phẩm mới";
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/admin/add-product.php'));
    exit;
}

// Get all categories
$categories = getAllCategories();

// Handle form submission
$message = '';
$success = false;

if (isset($_POST['add_product'])) {
    // Validate and sanitize input
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $features = trim($_POST['features']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : 0;
    $sku = trim($_POST['sku']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $status = $_POST['status'];

    // Validate images (comma-separated URLs)
    $images = [];
    if (!empty($_POST['images'])) {
        $imageUrls = explode("\n", trim($_POST['images']));
        foreach ($imageUrls as $url) {
            $url = trim($url);
            if (!empty($url)) {
                $images[] = $url;
            }
        }
    }

    // Basic validation
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Tên sản phẩm không được để trống';
    }

    if (empty($description)) {
        $errors[] = 'Mô tả sản phẩm không được để trống';
    }

    if ($price <= 0) {
        $errors[] = 'Giá sản phẩm phải lớn hơn 0';
    }

    if ($sale_price > 0 && $sale_price >= $price) {
        $errors[] = 'Giá khuyến mãi phải nhỏ hơn giá gốc';
    }

    if (empty($sku)) {
        $errors[] = 'Mã SKU không được để trống';
    }

    if ($stock < 0) {
        $errors[] = 'Số lượng tồn kho không được âm';
    }

    if (empty($images)) {
        $errors[] = 'Phải có ít nhất một hình ảnh sản phẩm';
    }

    // If no errors, add product
    if (empty($errors)) {
        $productData = [
            'name' => $name,
            'category_id' => $category_id,
            'price' => $price,
            'sale_price' => $sale_price,
            'description' => $description,
            'features' => $features,
            'stock' => $stock,
            'sku' => $sku,
            'images' => $images,
            'status' => $status,
            'discount_percent' => getDiscountPercentage($price, $sale_price)
        ];

        $result = addProduct($productData);

        if ($result['success']) {
            $message = $result['message'];
            $success = true;
            $_POST = [];
        } else {
            $message = $result['message'];
        }
    } else {
        $message = implode('<br>', $errors);
    }
}

require_once 'layout/header.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản lý /</span> Thêm sản phẩm mới
    </h4>

    <?php if (!empty($message)): ?>
    <div class="alert alert-<?= $success ? 'success' : 'danger' ?> alert-dismissible" role="alert">
      <?= $message ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Thông tin sản phẩm</h5>
            <a href="products.php" class="btn btn-secondary">
              <i class="bx bx-arrow-back me-1"></i> Quay lại
            </a>
          </div>
          <div class="card-body">
            <form method="post">
              <div class="row mb-3">
                <div class="col-md-8">
                  <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Mã SKU <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="sku" required value="<?= isset($_POST['sku']) ? htmlspecialchars($_POST['sku']) : '' ?>">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                  <select class="form-select" name="category_id" required>
                    <option value="">Chọn danh mục</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= isset($_POST['category_id']) && $_POST['category_id'] == $category['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Giá <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" class="form-control" name="price" min="0" step="1000" required value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>">
                    <span class="input-group-text"><?= $config['currency'] ?></span>
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Giá khuyến mãi</label>
                  <div class="input-group">
                    <input type="number" class="form-control" name="sale_price" min="0" step="1000" value="<?= isset($_POST['sale_price']) ? htmlspecialchars($_POST['sale_price']) : '' ?>">
                    <span class="input-group-text"><?= $config['currency'] ?></span>
                  </div>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" name="stock" min="0" required value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '' ?>">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Mô tả sản phẩm <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="4" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Đặc điểm nổi bật</label>
                <textarea class="form-control" name="features" rows="3"><?= isset($_POST['features']) ? htmlspecialchars($_POST['features']) : '' ?></textarea>
                <small class="text-muted">Nhập các đặc điểm kỹ thuật chính của sản phẩm</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Hình ảnh <span class="text-danger">*</span></label>
                <textarea class="form-control" name="images" rows="3" placeholder="Nhập URL hình ảnh, mỗi URL một dòng" required><?= isset($_POST['images']) ? htmlspecialchars($_POST['images']) : '' ?></textarea>
                <small class="text-muted">Nhập URL hình ảnh, mỗi URL một dòng</small>
              </div>

              <div class="mb-3">
                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                <select class="form-select" name="status" required>
                  <option value="active" <?= isset($_POST['status']) && $_POST['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                  <option value="inactive" <?= isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                </select>
              </div>

              <div class="text-end mt-4">
                <button type="reset" class="btn btn-secondary me-2">
                  <i class="bx bx-reset me-1"></i> Làm mới
                </button>
                <button type="submit" name="add_product" class="btn btn-primary">
                  <i class="bx bx-plus me-1"></i> Thêm sản phẩm
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->

<?php require_once 'layout/footer.php'; ?>