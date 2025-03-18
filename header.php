<?php
require_once 'config.php';
require_once 'functions.php';

// Initialize cart
initCart();
$cartCount = getCartCount();

// Get all categories
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . $config['site_name'] : $config['site_name'] ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $config['site_url'] ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $config['site_url'] ?>/assets/css/shopdunk-style.css">
    <!-- Custom JavaScript -->
    <script src="<?= $config['site_url'] ?>/assets/js/script.js"></script>
    <script>
        // Kiểm tra xem script đã được tải thành công chưa
        console.log('Header script loaded');
    </script>
</head>
<body>
    <!-- Header -->
    <header class="py-2 border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 col-6">
                    <a href="<?= $config['site_url'] ?>" class="text-decoration-none">
                        <h1 class="fs-3 m-0 fw-bold text-primary">TechShop</h1>
                    </a>
                </div>
                <div class="col-md-8 d-none d-md-block">
                    <ul class="nav justify-content-center">
                       
                        <?php foreach ($categories as $category): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="<?= $config['site_url'] ?>/category.php?slug=<?= $category['slug'] ?>"><?= htmlspecialchars($category['name']) ?></a>
                        </li>
                        <?php endforeach; ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="/track-order.php">Tra cứu</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#">Tin tức</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium" href="#">Khuyến mãi</a>
                        </li> -->
                    </ul>
                </div>
                <div class="col-md-2 col-6 text-end">
                    <div class="d-flex justify-content-end align-items-center position-relative">
                        <a onclick="TimKiem();" class="btn btn-link text-dark me-2" id="searchToggle">
                            <i class="fas fa-search"></i>
                        </a>
                        <div class="search-bar" id="searchForm" style="display: none;">
                            <form action="<?= $config['site_url'] ?>/search.php" method="get">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" placeholder="Tìm kiếm sản phẩm..." required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <a href="<?= $config['site_url'] ?>/cart.php" class="btn btn-link text-dark position-relative me-2">
                            <i class="fas fa-shopping-bag"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $cartCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <?php if (isset($_SESSION['user'])): ?>
                            <div class="dropdown">
                                <a href="#" class="btn btn-link text-dark dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Xin chào, <?= $_SESSION['user']['full_name'] ?></a></li>
                                    <li><a class="dropdown-item" href="#">Thông tin tài khoản</a></li>
                                    <li><a class="dropdown-item" href="<?= $config['site_url'] ?>/my-orders.php">Đơn hàng của tôi</a></li>
                                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?= $config['site_url'] ?>/admin/">Quản trị</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= $config['site_url'] ?>/logout.php">Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="<?= $config['site_url'] ?>/login.php" class="btn btn-link text-dark">
                                <i class="fas fa-user-circle"></i>
                            </a>
                         
                        <?php endif; ?>
                        <button class="navbar-toggler d-md-none ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">TechShop</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link py-2" href="<?= $config['site_url'] ?>">iPhone</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="#">iPad</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="#">Mac</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="#">Watch</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="#">Âm thanh</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="#">Phụ kiện</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="/track-order.php">Tra cứu đơn hàng</a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link py-2" href="#">Tin tức</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2" href="#">Khuyến mãi</a>
                </li> -->
            </ul>

            <hr>

            <form action="<?= $config['site_url'] ?>/search.php" method="get" class="mt-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Tìm kiếm sản phẩm..." required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <?php if (isset($_SESSION['user'])): ?>
                <div class="mt-3">
                    <p class="mb-2">Xin chào, <?= $_SESSION['user']['full_name'] ?></p>
                    <a href="<?= $config['site_url'] ?>/my-orders.php" class="btn btn-outline-primary mb-2">Đơn hàng của tôi</a>
                    <div class="d-grid gap-2">
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="<?= $config['site_url'] ?>/admin/" class="btn btn-outline-primary">Quản trị</a>
                        <?php endif; ?>
                        <a href="<?= $config['site_url'] ?>/logout.php" class="btn btn-outline-danger">Đăng xuất</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-grid gap-2 mt-3">
                    <a href="<?= $config['site_url'] ?>/login.php" class="btn btn-primary">Đăng nhập</a>
                    <a href="<?= $config['site_url'] ?>/register.php" class="btn btn-outline-primary">Đăng ký</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main content container -->
    <main class="py-4">REM Update 18 - Improve header 
REM Update 18 - Improve header 
