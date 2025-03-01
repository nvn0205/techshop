<?php

$pageTitle = "Trang chủ";
require_once 'config.php';
require_once 'functions.php';

    include 'header.php';
?>

<!-- Banner Slider -->
<div id="mainBanner" class="carousel slide mb-4" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://shopdunk.com/images/uploaded/banner/Banner%202025/Thang_3/T3.3DM/banner%20iphone%2016e-3_PC%20(3).png" class="d-block w-100" alt="MacBook Air M2">
        </div>
        <div class="carousel-item">
            <img src="https://shopdunk.com/images/uploaded/banner/Banner%202025/Thang_3/T3.3/banner%20Macbook%20air%20M4_PC.png" class="d-block w-100" alt="iPhone 15">
        </div>
        <div class="carousel-item">
            <img src="https://shopdunk.com/images/uploaded/banner/Banner%202025/Thang_3/T3.4/banner%20iP16sr_PC.png" alt="Apple Watch Series 9">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainBanner" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Trước</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainBanner" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Tiếp</span>
    </button>
</div>

<div class="container">
    <!-- Category Icons -->
    <div class="row mb-5 py-4">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold fs-4">Danh mục sản phẩm</h2>
        </div>
        <?php if ($categories): ?>
            <?php foreach(array_slice($categories, 0, 6) as $category): ?>
            <div class="col-4 col-md-2 text-center mb-4">
                <a href="<?= $config['site_url'] ?>/category.php?slug=<?= $category['slug'] ?>" class="text-decoration-none">
                    <div class="category-icon-wrapper mb-3">
                        <?php if($category['slug'] === 'iphone'): ?>
                            <i class="fas fa-mobile-alt fa-2x"></i>
                        <?php elseif($category['slug'] === 'macbook'): ?>
                            <i class="fas fa-laptop fa-2x"></i>
                        <?php elseif($category['slug'] === 'ipad'): ?>
                            <i class="fas fa-tablet-alt fa-2x"></i>
                        <?php elseif($category['slug'] === 'apple-watch'): ?>
                            <i class="fas fa-clock fa-2x"></i>
                        <?php elseif($category['slug'] === 'am-thanh'): ?>
                            <i class="fas fa-headphones fa-2x"></i>
                        <?php else: ?>
                            <i class="fas fa-cogs fa-2x"></i>
                        <?php endif; ?>
                    </div>
                    <h6 class="fw-bold"><?= $category['name'] ?></h6>
                </a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Product Sections for each category -->
    <?php foreach($categories as $category): ?>
        <?php 
        $categoryProducts = getCategoryProducts($category['slug'], 4);
        if ($categoryProducts): 
        ?>
        <section class="mb-5">
            <h2 class="text-center mb-4"><?= $category['name'] ?></h2>
            <div class="row">
                <?php foreach($categoryProducts as $product): ?>
                <div class="col-6 col-md-3 mb-4">
                    <div class="card product-card h-100 border-0 shadow-sm position-relative">
                        <?php if(isset($product['is_new']) && $product['is_new']): ?>
                        <div class="badge bg-success position-absolute top-0 start-0 m-2">Mới</div>
                        <?php endif; ?>

                        <?php if(isset($product['discount_percent']) && $product['discount_percent'] > 0): ?>
                        <div class="badge bg-danger position-absolute top-0 start-0 m-2">Giảm <?= $product['discount_percent'] ?>%</div>
                        <?php endif; ?>

                        <?php if(isset($product['installment']) && $product['installment']): ?>
                        <div class="badge bg-primary position-absolute top-0 end-0 m-2">Trả góp 0%</div>
                        <?php endif; ?>

                        <a href="product.php?id=<?= $product['id'] ?>">
                            <img src="<?= $product['image'] ?>" class="card-img-top p-4" alt="<?= $product['name'] ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title text-center"><?= $product['name'] ?></h5>
                            <div class="text-center">
                                <?php if(isset($product['sale_price']) && $product['sale_price'] > 0): ?>
                                    <p class="mb-0 text-primary fw-bold"><?= number_format($product['sale_price']) ?>đ</p>
                                    <p class="text-decoration-line-through text-muted"><?= number_format($product['price']) ?>đ</p>
                                <?php else: ?>
                                    <p class="mb-0 text-primary fw-bold"><?= number_format($product['price']) ?>đ</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center">
                <a href="category.php?slug=<?= $category['slug'] ?>" class="btn btn-outline-primary rounded-pill">Xem tất cả <?= $category['name'] ?></a>
            </div>
        </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Promo Banner -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="promo-banner">
                <img src="https://shopdunk.com/images/uploaded/banner/bonus%20banner/xx1/L%C3%AA%20B%E1%BB%91ng.png" class="img-fluid rounded" alt="Khuyến mãi">
            </div>
        </div>
    </div>


    <!-- Info Banners -->
    <section class="mb-5">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-shipping-fast text-primary fa-3x mb-3"></i>
                        <h4>Giao hàng nhanh chóng</h4>
                        <p class="mb-0">Giao hàng toàn quốc trong 24h</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-shield-alt text-primary fa-3x mb-3"></i>
                        <h4>Bảo hành chính hãng</h4>
                        <p class="mb-0">Sản phẩm chính hãng 100%</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-headset text-primary fa-3x mb-3"></i>
                        <h4>Hỗ trợ 24/7</h4>
                        <p class="mb-0">Hotline: 1900 1234</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>
REM Update 4 - Improve homepage 
