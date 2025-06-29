<?php
$pageTitle = "Tìm kiếm sản phẩm";
require_once 'config.php';
require_once 'functions.php';

// Get search keyword from URL
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// If no keyword provided, redirect to homepage
if (empty($keyword)) {
    header('Location: ' . $config['site_url']);
    exit;
}

$pageTitle = "Kết quả tìm kiếm: " . $keyword;

// Search products
$products = searchProducts($keyword);

// Handle price filter
if (isset($_GET['price'])) {
    $priceRange = $_GET['price'];
    $products = array_filter($products, function($product) use ($priceRange) {
        $price = isset($product['sale_price']) && $product['sale_price'] > 0 ? $product['sale_price'] : $product['price'];
        switch($priceRange) {
            case 'under5m':
                return $price < 5000000;
            case '5m-10m':
                return $price >= 5000000 && $price < 10000000;
            case '10m-20m':
                return $price >= 10000000 && $price < 20000000;
            case '20m-30m':
                return $price >= 20000000 && $price < 30000000;
            case 'over30m':
                return $price >= 30000000;
            default:
                return true;
        }
    });
}

// Handle status filter
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'in_stock') {
        $products = array_filter($products, function($product) {
            return $product['stock'] > 0;
        });
    } else if ($_GET['status'] === 'on_sale') {
        $products = array_filter($products, function($product) {
            return isset($product['sale_price']) && $product['sale_price'] > 0;
        });
    }
}

// Handle sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
if ($sort === 'price_asc') {
    usort($products, function($a, $b) {
        $a_price = isset($a['sale_price']) && $a['sale_price'] > 0 ? $a['sale_price'] : $a['price'];
        $b_price = isset($b['sale_price']) && $b['sale_price'] > 0 ? $b['sale_price'] : $b['price'];
        return $a_price - $b_price;
    });
} elseif ($sort === 'price_desc') {
    usort($products, function($a, $b) {
        $a_price = isset($a['sale_price']) && $a['sale_price'] > 0 ? $a['sale_price'] : $a['price'];
        $b_price = isset($b['sale_price']) && $b['sale_price'] > 0 ? $b['sale_price'] : $b['price'];
        return $b_price - $a_price;
    });
} elseif ($sort === 'name_asc') {
    usort($products, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
} elseif ($sort === 'name_desc') {
    usort($products, function($a, $b) {
        return strcmp($b['name'], $a['name']);
    });
} elseif ($sort === 'newest') {
    usort($products, function($a, $b) {
        return strtotime($b['date_added']) - strtotime($a['date_added']);
    });
}

include 'header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $config['site_url'] ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Danh mục sản phẩm</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php 
                        $categories = getAllCategories();
                        foreach($categories as $category): 
                        ?>
                        <li class="list-group-item">
                            <a href="<?= $config['site_url'] ?>/category.php?slug=<?= $category['slug'] ?>" class="text-decoration-none text-dark">
                                <?= $category['name'] ?>
                            </a>
                        </li>
                        <?php 
                        endforeach; 
                        ?>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lọc sản phẩm</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Khoảng giá</h6>
                    <form action="" method="get">
                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price1" value="under5m" <?= isset($_GET['price']) && $_GET['price'] === 'under5m' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="price1">
                                    Dưới 5 triệu
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price2" value="5m-10m" <?= isset($_GET['price']) && $_GET['price'] === '5m-10m' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="price2">
                                    5 - 10 triệu
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price3" value="10m-20m" <?= isset($_GET['price']) && $_GET['price'] === '10m-20m' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="price3">
                                    10 - 20 triệu
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price4" value="20m-30m" <?= isset($_GET['price']) && $_GET['price'] === '20m-30m' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="price4">
                                    20 - 30 triệu
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price5" value="over30m" <?= isset($_GET['price']) && $_GET['price'] === 'over30m' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="price5">
                                    Trên 30 triệu
                                </label>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">Tình trạng</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status1" value="in_stock" <?= isset($_GET['status']) && $_GET['status'] === 'in_stock' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status1">
                                    Còn hàng
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status2" value="on_sale" <?= isset($_GET['status']) && $_GET['status'] === 'on_sale' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status2">
                                    Đang giảm giá
                                </label>
                            </div>
                        </div>
                        
                        <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Kết quả tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</h1>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Sắp xếp
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>" 
                               href="?keyword=<?= urlencode($keyword) ?>&sort=newest">Mới nhất</a></li>
                        <li><a class="dropdown-item <?= $sort === 'price_asc' ? 'active' : '' ?>" 
                               href="?keyword=<?= urlencode($keyword) ?>&sort=price_asc">Giá: Thấp đến cao</a></li>
                        <li><a class="dropdown-item <?= $sort === 'price_desc' ? 'active' : '' ?>" 
                               href="?keyword=<?= urlencode($keyword) ?>&sort=price_desc">Giá: Cao đến thấp</a></li>
                        <li><a class="dropdown-item <?= $sort === 'name_asc' ? 'active' : '' ?>" 
                               href="?keyword=<?= urlencode($keyword) ?>&sort=name_asc">Tên: A-Z</a></li>
                        <li><a class="dropdown-item <?= $sort === 'name_desc' ? 'active' : '' ?>" 
                               href="?keyword=<?= urlencode($keyword) ?>&sort=name_desc">Tên: Z-A</a></li>
                    </ul>
                </div>
            </div>
            
            <?php if (empty($products)): ?>
            <div class="alert alert-info">
                Không tìm thấy sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>".
            </div>
            <?php else: ?>
            
            <div class="row">
                <?php foreach($products as $product): ?>
                <div class="col-6 col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <?php if(isset($product['discount_percent']) && $product['discount_percent'] > 0): ?>
                        <div class="discount-badge">-<?= $product['discount_percent'] ?>%</div>
                        <?php endif; ?>
                        <a href="<?= $config['site_url'] ?>/product.php?id=<?= $product['id'] ?>">
                            <img src="<?= $product['images'][0] ?>" class="card-img-top product-img" alt="<?= $product['name'] ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title product-title">
                                <a href="<?= $config['site_url'] ?>/product.php?id=<?= $product['id'] ?>" class="text-decoration-none"><?= $product['name'] ?></a>
                            </h5>
                            <div class="mt-auto">
                                <?php if(isset($product['sale_price']) && $product['sale_price'] > 0): ?>
                                <p class="card-text mb-1">
                                    <span class="fw-bold text-primary"><?= formatPrice($product['sale_price']) ?></span>
                                    <span class="text-decoration-line-through text-muted ms-2"><?= formatPrice($product['price']) ?></span>
                                </p>
                                <?php else: ?>
                                <p class="card-text mb-1">
                                    <span class="fw-bold text-primary"><?= formatPrice($product['price']) ?></span>
                                </p>
                                <?php endif; ?>
                                <a href="<?= $config['site_url'] ?>/product.php?id=<?= $product['id'] ?>" class="btn btn-primary mt-2 w-100">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Trước</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Tiếp</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
