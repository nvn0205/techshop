<?php
$pageTitle = "Chi tiết sản phẩm";
require_once 'config.php';
require_once 'functions.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product details
$product = getProductById($product_id);

// If product not found, redirect to homepage
if (!$product) {
    header('Location: ' . $config['site_url']);
    exit;
}

$pageTitle = $product['name'];

// Handle add to cart
$message = '';
if (isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $result = addToCart($product_id, $quantity);
    $message = $result['message'];
}

// Get related products (same category)
$relatedProducts = getProductsByCategory($product['category_id']);
// Remove current product from related products and limit to 4
$relatedProducts = array_filter($relatedProducts, function($item) use ($product_id) {
    return $item['id'] != $product_id;
});
$relatedProducts = array_slice($relatedProducts, 0, 4);

include 'header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $config['site_url'] ?>">Trang chủ</a></li>
            <?php
            $category = getCategoryById($product['category_id']);
            if ($category):
            ?>
            <li class="breadcrumb-item"><a href="<?= $config['site_url'] ?>/category.php?slug=<?= $category['slug'] ?>"><?= $category['name'] ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= $product['name'] ?></li>
        </ol>
    </nav>
    
    <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="row mb-5">
        <!-- Product Images -->
        <div class="col-md-6 mb-4">
            <div class="product-images">
                <div class="main-image mb-3">
                    <img src="<?= $product['images'][0] ?>" class="img-fluid" id="mainProductImage" alt="<?= $product['name'] ?>">
                </div>
                <?php if (count($product['images']) > 1): ?>
                <div class="row thumbnail-images">
                    <?php foreach($product['images'] as $index => $image): ?>
                    <div class="col-3">
                        <img src="<?= $image ?>" class="img-fluid thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                             onclick="changeMainImage(this.src)" alt="<?= $product['name'] ?> <?= $index + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="mb-3"><?= $product['name'] ?></h1>
            
            <div class="mb-3">
                <?php if(isset($product['sale_price']) && $product['sale_price'] > 0): ?>
                <h2 class="text-primary mb-0"><?= formatPrice($product['sale_price']) ?></h2>
                <p class="mb-0">
                    <span class="text-decoration-line-through text-muted"><?= formatPrice($product['price']) ?></span>
                    <span class="badge bg-danger ms-2">-<?= $product['discount_percent'] ?>%</span>
                </p>
                <?php else: ?>
                <h2 class="text-primary"><?= formatPrice($product['price']) ?></h2>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <p class="text-success">
                    <i class="fas fa-check-circle"></i> 
                    <?= $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng' ?>
                </p>
                <p class="mb-1"><strong>SKU:</strong> <?= $product['sku'] ?></p>
                <p><strong>Danh mục:</strong> 
                    <?php if ($category): ?>
                    <a href="<?= $config['site_url'] ?>/category.php?slug=<?= $category['slug'] ?>"><?= $category['name'] ?></a>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="product-description mb-4">
                <h4>Mô tả:</h4>
                <p><?= $product['description'] ?></p>
            </div>
            
            <div class="product-features mb-4">
                <h4>Đặc điểm nổi bật:</h4>
                <p><?= $product['features'] ?></p>
            </div>
            
            <!-- Add to Cart Form -->
            <form action="" method="post" class="d-flex flex-column mb-4">
                <div class="d-flex align-items-center mb-3">
                    <label for="quantity" class="me-3">Số lượng:</label>
                    <div class="input-group" style="width: 150px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">-</button>
                        <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(<?= $product['stock'] ?>)">+</button>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg<?= $product['stock'] <= 0 ? ' disabled' : '' ?>">
                        <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                    </button>
                    <button type="button" class="btn btn-success btn-lg<?= $product['stock'] <= 0 ? ' disabled' : '' ?>" 
                            onclick="buyNow(<?= $product['id'] ?>)">
                        <i class="fas fa-bolt me-2"></i> Mua ngay
                    </button>
                </div>
            </form>
            
            <div class="product-policy">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-truck fs-5 me-3 text-primary"></i>
                    <span>Giao hàng miễn phí toàn quốc</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-sync-alt fs-5 me-3 text-primary"></i>
                    <span>Đổi trả trong 10 ngày nếu sản phẩm lỗi</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-shield-alt fs-5 me-3 text-primary"></i>
                    <span>Bảo hành chính hãng 12 tháng</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-credit-card fs-5 me-3 text-primary"></i>
                    <span>Thanh toán nhiều hình thức</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="mb-5">
        <h3 class="mb-4">Sản phẩm liên quan</h3>
        <div class="row">
            <?php foreach($relatedProducts as $relatedProduct): ?>
            <div class="col-6 col-md-3 mb-4">
                <div class="card product-card h-100">
                    <?php if(isset($relatedProduct['discount_percent']) && $relatedProduct['discount_percent'] > 0): ?>
                    <div class="discount-badge">-<?= $relatedProduct['discount_percent'] ?>%</div>
                    <?php endif; ?>
                    <a href="<?= $config['site_url'] ?>/product.php?id=<?= $relatedProduct['id'] ?>">
                        <img src="<?= $relatedProduct['images'][0] ?>" class="card-img-top product-img" alt="<?= $relatedProduct['name'] ?>">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title product-title">
                            <a href="<?= $config['site_url'] ?>/product.php?id=<?= $relatedProduct['id'] ?>" class="text-decoration-none"><?= $relatedProduct['name'] ?></a>
                        </h5>
                        <div class="mt-auto">
                            <?php if(isset($relatedProduct['sale_price']) && $relatedProduct['sale_price'] > 0): ?>
                            <p class="card-text mb-1">
                                <span class="fw-bold text-primary"><?= formatPrice($relatedProduct['sale_price']) ?></span>
                                <span class="text-decoration-line-through text-muted ms-2"><?= formatPrice($relatedProduct['price']) ?></span>
                            </p>
                            <?php else: ?>
                            <p class="card-text mb-1">
                                <span class="fw-bold text-primary"><?= formatPrice($relatedProduct['price']) ?></span>
                            </p>
                            <?php endif; ?>
                            <a href="<?= $config['site_url'] ?>/product.php?id=<?= $relatedProduct['id'] ?>" class="btn btn-primary mt-2 w-100">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('mainProductImage').src = src;
    
    // Update active state on thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(thumb => {
        if (thumb.src === src) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

function increaseQuantity(maxStock) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue < maxStock) {
        quantityInput.value = currentValue + 1;
    }
}

function buyNow(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    if (quantity > 0) {
        // Show loading indicator
        const buyNowBtn = document.querySelector('button[onclick*="buyNow"]');
        const originalText = buyNowBtn.innerHTML;
        buyNowBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
        buyNowBtn.disabled = true;

        // Add to cart first
        const formData = new FormData();
        formData.append('add_to_cart', '1');
        formData.append('quantity', quantity);

        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(() => {
            // Redirect to checkout page
            window.location.href = '<?= $config['site_url'] ?>/checkout.php';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.');

            // Reset button state
            buyNowBtn.innerHTML = originalText;
            buyNowBtn.disabled = false;
        });
    } else {
        alert('Vui lòng chọn số lượng sản phẩm');
    }
}

</script>

<?php include 'footer.php'; ?>
