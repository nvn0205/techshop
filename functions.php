<?php
require_once 'config.php';

// Get all products
function getAllProducts() {
    global $db;
    $products = [];

    $result = mysqli_query($db, "SELECT * FROM products ORDER BY id");

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            // Process the images JSON
            if (isset($product['images']) && is_string($product['images'])) {
                $product['images'] = json_decode($product['images'], true) ?: [];
            } else if (!isset($product['images'])) {
                $product['images'] = [];
            }

            // Set default image
            $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';
            $products[] = $product;
        }
        mysqli_free_result($result);
    }

    return $products;
}


function prepareImagesForStorage($images) { // Chuyển mảng ảnh thành chuỗi JSON
    if (is_array($images)) {
        return json_encode($images);
    }
    return json_encode([]);
}
// Lấy sản phẩm theo ID
function getProductById($id) {
    global $db;

    $query = "SELECT * FROM products WHERE id = " . intval($id);
    $result = mysqli_query($db, $query);

    if ($result && $product = mysqli_fetch_assoc($result)) {

        if (isset($product['images']) && is_string($product['images'])) {
            $product['images'] = json_decode($product['images'], true) ?: [];
        } else if (!isset($product['images'])) {
            $product['images'] = [];
        }

        $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';

        mysqli_free_result($result);
        return $product;
    }

    if ($result) {
        mysqli_free_result($result);
    }
    return null;
}

// Lấy sản phẩm theo danh mục
function getProductsByCategory($category_id) {
    global $db;

    $query = "SELECT * FROM products WHERE category_id = " . intval($category_id) . " AND status = 'active'";
    $result = mysqli_query($db, $query);
    $products = [];

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            if (isset($product['images']) && is_string($product['images'])) {
                $product['images'] = json_decode($product['images'], true) ?: [];
            } else if (!isset($product['images'])) {
                $product['images'] = [];
            }

            // Set default image
            $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';
            $products[] = $product;
        }
        mysqli_free_result($result);
    }

    return $products;
}

// Lấy tất cả danh mục
function getAllCategories() {
    global $db;
    $categories = [];

    $result = mysqli_query($db, "SELECT * FROM categories");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        mysqli_free_result($result);
    }

    return $categories;
}

// Lấy danh mục theo ID
function getCategoryById($id) {
    global $db;

    $query = "SELECT * FROM categories WHERE id = " . intval($id);
    $result = mysqli_query($db, $query);

    if ($result && $category = mysqli_fetch_assoc($result)) {
        mysqli_free_result($result);
        return $category;
    }

    if ($result) {
        mysqli_free_result($result);
    }
    return null;
}

// Lấy danh mục theo slug
function getCategoryBySlug($slug) {
    global $db;

    $slug = mysqli_real_escape_string($db, $slug);
    $query = "SELECT * FROM categories WHERE slug = '$slug'";
    $result = mysqli_query($db, $query);

    if ($result && $category = mysqli_fetch_assoc($result)) {
        mysqli_free_result($result);
        return $category;
    }

    if ($result) {
        mysqli_free_result($result);
    }
    return null;
}

// Tìm kiếm sản phẩm
function searchProducts($keyword) {
    global $db;
    $products = [];

    $keyword = mysqli_real_escape_string($db, $keyword);
    $query = "SELECT * FROM products WHERE (name LIKE '%$keyword%' OR description LIKE '%$keyword%') AND status = 'active'";
    $result = mysqli_query($db, $query);

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            if (isset($product['images']) && is_string($product['images'])) {
                $product['images'] = json_decode($product['images'], true) ?: [];
            } else if (!isset($product['images'])) {
                $product['images'] = [];
            }

   // Đặt ảnh mặc định
            $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';
            $products[] = $product;
        }
        mysqli_free_result($result);
    }

    return $products;
}

// Định dạng giá
function formatPrice($price) {
    global $config;
    $price = is_null($price) ? 0 : $price;
    return number_format($price, 0, ',', '.') . $config['currency'];
}

// Lấy phần trăm giảm giá
function getDiscountPercentage($original, $sale) {
    if ($original > 0 && $sale > 0 && $original > $sale) {
        return round(($original - $sale) / $original * 100);
    }
    return 0;
}

// Kiểm tra đăng nhập
function authenticateUser($username, $password) {
    global $db;

    $username = mysqli_real_escape_string($db, $username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($db, $query);

    if ($result && $user = mysqli_fetch_assoc($result)) {
        mysqli_free_result($result);
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }

    if ($result) {
        mysqli_free_result($result);
    }
    return false;
}
// Đăng ký người dùng
function registerUser($username, $password, $email, $fullName) {
    global $db;

    // Kiểm tra tên đăng nhập đã tồn tại chưa
    $username = mysqli_real_escape_string($db, $username);
    $query = "SELECT COUNT(*) as count FROM users WHERE username = '$username'";
    $result = mysqli_query($db, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if ($row['count'] > 0) {
            mysqli_free_result($result);
            return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
        }
    }

    // Kiểm tra email đã tồn tại chưa
    $email = mysqli_real_escape_string($db, $email);
    $query = "SELECT COUNT(*) as count FROM users WHERE email = '$email'";
    $result = mysqli_query($db, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if ($row['count'] > 0) {
            mysqli_free_result($result);
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }
    }

    //  Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $fullName = mysqli_real_escape_string($db, $fullName);

    $query = "INSERT INTO users (username, password, email, full_name, role, updated_at) VALUES ('$username', '$hashedPassword', '$email', '$fullName', 'customer', NOW())";

    if (mysqli_query($db, $query)) {
        $newUserId = mysqli_insert_id($db);
        return ['success' => true, 'message' => 'Đăng ký thành công', 'user_id' => $newUserId];
    }

    return ['success' => false, 'message' => 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.'];
}

// Lấy người dùng theo ID
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function addToCart($product_id, $quantity = 1) {
    $product = getProductById($product_id);

    if (!$product) {
        return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
    }

    initCart();

    // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => isset($product['sale_price']) && $product['sale_price'] > 0 ? $product['sale_price'] : $product['price'],
            'quantity' => $quantity,
            'image' => $product['images'][0] ?? ''
        ];
    }

    return ['success' => true, 'message' => 'Đã thêm vào giỏ hàng'];
}

function updateCartItem($product_id, $quantity) {
    initCart();

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            if ($quantity <= 0) {
                return removeCartItem($product_id);
            }
            $item['quantity'] = $quantity;
            return ['success' => true, 'message' => 'Đã cập nhật giỏ hàng'];
        }
    }

    return ['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng'];
}

function removeCartItem($product_id) {
    initCart();

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
            return ['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'];
        }
    }

    return ['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng'];
}

function getCartTotal() {
    initCart();

    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return $total;
}

function getCartCount() {
    initCart();

    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }

    return $count;
}

function clearCart() {
    $_SESSION['cart'] = [];
}

//  Thêm sản phẩm
function addProduct($data) {
    global $db;

    //  Tính phần trăm giảm giá nếu chưa có
    $discount_percent = isset($data['discount_percent']) 
        ? (int)$data['discount_percent'] 
        : getDiscountPercentage($data['price'], $data['sale_price'] ?? 0);

    //  Chuyển mảng ảnh thành JSON để lưu trữ trong MySQL
    $images = isset($data['images']) ? json_encode($data['images']) : json_encode([]);

    $name = mysqli_real_escape_string($db, $data['name']);
    $description = mysqli_real_escape_string($db, $data['description']);
    $features = mysqli_real_escape_string($db, $data['features']);
    $sku = mysqli_real_escape_string($db, $data['sku']);
    $status = mysqli_real_escape_string($db, $data['status']);
    $images = mysqli_real_escape_string($db, $images);

    $query = "INSERT INTO products (name, category_id, price, sale_price, description, features, stock, sku, images, discount_percent, status, updated_at) 
              VALUES ('$name', " . intval($data['category_id']) . ", " . floatval($data['price']) . ", " . 
              (isset($data['sale_price']) && $data['sale_price'] > 0 ? floatval($data['sale_price']) : 0) . 
              ", '$description', '$features', " . intval($data['stock']) . ", '$sku', '$images', $discount_percent, '$status', NOW())";

    if (mysqli_query($db, $query)) {
        $newId = mysqli_insert_id($db);
        return ['success' => true, 'message' => 'Sản phẩm đã được thêm thành công', 'id' => $newId];
    }

    return ['success' => false, 'message' => 'Đã xảy ra lỗi khi thêm sản phẩm: ' . mysqli_error($db)];
}

function updateProduct($id, $data) {
    global $db;

    //  Tính phần trăm giảm giá nếu chưa có
    $discount_percent = isset($data['discount_percent']) 
        ? (int)$data['discount_percent'] 
        : getDiscountPercentage($data['price'], $data['sale_price'] ?? 0);

    // Chuyển mảng ảnh thành JSON để lưu trữ trong MySQL
    $images = isset($data['images']) ? json_encode($data['images']) : json_encode([]);

    $name = mysqli_real_escape_string($db, $data['name']);
    $description = mysqli_real_escape_string($db, $data['description']);
    $features = mysqli_real_escape_string($db, $data['features']);
    $sku = mysqli_real_escape_string($db, $data['sku']);
    $status = mysqli_real_escape_string($db, $data['status']);
    $images = mysqli_real_escape_string($db, $images);

    $query = "UPDATE products SET 
              name = '$name',
              category_id = " . intval($data['category_id']) . ",
              price = " . floatval($data['price']) . ",
              sale_price = " . (isset($data['sale_price']) && $data['sale_price'] > 0 ? floatval($data['sale_price']) : 0) . ",
              description = '$description',
              features = '$features',
              stock = " . intval($data['stock']) . ",
              sku = '$sku',
              images = '$images',
              discount_percent = $discount_percent,
              status = '$status',
              updated_at = NOW()
              WHERE id = " . intval($id);

    if (mysqli_query($db, $query)) {
        return ['success' => true, 'message' => 'Sản phẩm đã được cập nhật thành công'];
    }

    return ['success' => false, 'message' => 'Đã xảy ra lỗi khi cập nhật sản phẩm: ' . mysqli_error($db)];
}

function deleteProduct($id) {
    global $db;

    //  Kiểm tra sản phẩm đã có trong đơn hàng chưa
    $query = "SELECT COUNT(*) as count FROM order_items WHERE product_id = " . intval($id);
    $result = mysqli_query($db, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if ($row['count'] > 0) {
            mysqli_free_result($result);
            return ['success' => false, 'message' => 'Không thể xóa sản phẩm vì đã có trong đơn hàng'];
        }
    }

    // Xóa sản phẩm
    $query = "DELETE FROM products WHERE id = " . intval($id);
    if (mysqli_query($db, $query)) {
        return ['success' => true, 'message' => 'Sản phẩm đã được xóa thành công'];
    }

    return ['success' => false, 'message' => 'Đã xảy ra lỗi khi xóa sản phẩm: ' . mysqli_error($db)];
}

// Tạo đơn hàng
function createOrder($userData, $items, $total) {
    global $db;

    mysqli_begin_transaction($db);

    try {
        $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'NULL';
        $paymentMethod = mysqli_real_escape_string($db, $userData['payment_method'] ?? 'COD');
        $notes = mysqli_real_escape_string($db, $userData['note'] ?? '');
        $address = mysqli_real_escape_string($db, $userData['address']);
        $phone = mysqli_real_escape_string($db, $userData['phone']);
        $name = mysqli_real_escape_string($db, $userData['name']);
        $email = mysqli_real_escape_string($db, $userData['email']);

        $query = "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, phone, notes, full_name, email, updated_at)
                  VALUES ($userId, " . floatval($total) . ", 'pending', '$paymentMethod', '$address', '$phone', '$notes', '$name', '$email', NOW())";

        if (!mysqli_query($db, $query)) {
            throw new Exception(mysqli_error($db));
        }

        $orderId = mysqli_insert_id($db);

        foreach ($items as $item) {
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                      VALUES ($orderId, " . intval($item['id']) . ", " . intval($item['quantity']) . ", " . floatval($item['price']) . ")";

            if (!mysqli_query($db, $query)) {
                throw new Exception(mysqli_error($db));
            }

            $query = "UPDATE products SET stock = stock - " . intval($item['quantity']) . ", updated_at = NOW() WHERE id = " . intval($item['id']);

            if (!mysqli_query($db, $query)) {
                throw new Exception(mysqli_error($db));
            }
        }

        mysqli_commit($db);
        return ['success' => true, 'message' => 'Đơn hàng đã được tạo thành công', 'order_id' => $orderId];
    } catch (Exception $e) {
        mysqli_rollback($db);
        error_log('Database error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Đã xảy ra lỗi khi tạo đơn hàng: ' . $e->getMessage()];
    }
}
// Lấy tất cả đơn hàng
function getAllOrders() {
    global $db;
    $orders = [];

    $query = "SELECT o.*, u.username, u.email AS user_email, u.full_name AS user_full_name
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.id
              ORDER BY o.created_at DESC";

    $result = mysqli_query($db, $query);

    if ($result) {
        while ($order = mysqli_fetch_assoc($result)) {
            $order['items'] = getOrderItems($order['id']);
            $orders[] = $order;
        }
        mysqli_free_result($result);
    }

    return $orders;
}

function getOrderItems($orderId) {
    global $db;
    $items = [];

    $query = "SELECT oi.*, p.name, p.sku, p.images
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = " . intval($orderId);

    $result = mysqli_query($db, $query);

    if ($result) {
        while ($item = mysqli_fetch_assoc($result)) {
            if (isset($item['images']) && is_string($item['images'])) {
                $item['images'] = json_decode($item['images'], true) ?: [];
                $item['image'] = count($item['images']) > 0 ? $item['images'][0] : '';
            } else if (!isset($item['images'])) {
                $item['images'] = [];
                $item['image'] = '';
            }
            $items[] = $item;
        }
        mysqli_free_result($result);
    }

    return $items;
}

function getUserOrders($user_id) {
    global $db;
    $orders = [];

    $query = "SELECT * FROM orders WHERE user_id = " . intval($user_id) . " ORDER BY created_at DESC";
    $result = mysqli_query($db, $query);

    if ($result) {
        while ($order = mysqli_fetch_assoc($result)) {
            $order['items'] = getOrderItems($order['id']);
            $orders[] = $order;
        }
        mysqli_free_result($result);
    }

    return $orders;
}

function getOrderById($id) {
    global $db;

    $query = "SELECT o.*, u.username, u.email AS user_email, u.full_name AS user_full_name
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.id
              WHERE o.id = " . intval($id);

    $result = mysqli_query($db, $query);

    if ($result && $order = mysqli_fetch_assoc($result)) {
        $order['items'] = getOrderItems($order['id']);
        mysqli_free_result($result);
        return $order;
    }

    if ($result) {
        mysqli_free_result($result);
    }
    return null;
}

function updateOrderStatus($id, $status) {
    global $db;

    $status = mysqli_real_escape_string($db, $status);
    $query = "UPDATE orders SET status = '$status' WHERE id = " . intval($id);

    if (mysqli_query($db, $query)) {
        return ['success' => true, 'message' => 'Trạng thái đơn hàng đã được cập nhật'];
    }

    return ['success' => false, 'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng: ' . mysqli_error($db)];
}

function getFeaturedProducts($limit = 8) {
    global $db;
    $products = [];

    $query = "SELECT * FROM products WHERE status = 'active' ORDER BY discount_percent DESC LIMIT " . intval($limit);
    $result = mysqli_query($db, $query);

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            if (isset($product['images']) && is_string($product['images'])) {
                $product['images'] = json_decode($product['images'], true) ?: [];
            } else if (!isset($product['images'])) {
                $product['images'] = [];
            }

            $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';
            $products[] = $product;
        }
        mysqli_free_result($result);
    }

    return $products;
}

function getCategoryProducts($slug, $limit = 8) {
    global $db;

    $slug = mysqli_real_escape_string($db, $slug);
    $query = "SELECT id FROM categories WHERE slug = '$slug'";
    $result = mysqli_query($db, $query);

    if (!$result || !($category = mysqli_fetch_assoc($result))) {
        if ($result) mysqli_free_result($result);
        return [];
    }

    mysqli_free_result($result);

    $query = "SELECT *, DATEDIFF(NOW(), created_at) <= 30 as is_new, price >= 3000000 as installment
              FROM products 
              WHERE category_id = " . intval($category['id']) . " AND status = 'active'
              ORDER BY created_at DESC 
              LIMIT " . intval($limit);

    $result = mysqli_query($db, $query);
    $products = [];

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            if (isset($product['images']) && is_string($product['images'])) {
                $product['images'] = json_decode($product['images'], true) ?: [];
            } else if (!isset($product['images'])) {
                $product['images'] = [];
            }

            $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';

            if (!isset($product['discount_percent']) && isset($product['price']) && isset($product['sale_price']) && $product['sale_price'] > 0) {
                $product['discount_percent'] = getDiscountPercentage($product['price'], $product['sale_price']);
            }

            $products[] = $product;
        }
        mysqli_free_result($result);
    }

    return $products;
}

function getNewProducts($limit = 8) {
    global $db;
    $products = [];

    $query = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT " . intval($limit);
    $result = mysqli_query($db, $query);

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            if (isset($product['images']) && is_string($product['images'])) {
                $product['images'] = json_decode($product['images'], true) ?: [];
            } else if (!isset($product['images'])) {
                $product['images'] = [];
            }

            $product['image'] = count($product['images']) > 0 ? $product['images'][0] : '';
            $products[] = $product;
        }
        mysqli_free_result($result);
    }

    return $products;
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function getAllUsers() {
    global $db;
    $users = [];

    $result = mysqli_query($db, "SELECT * FROM users ORDER BY id");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_free_result($result);
    }

    return $users;
}

function getStatusText($status) {
    switch ($status) {
        case 'pending':
            return 'Chờ xử lý';
        case 'processing':
            return 'Đang xử lý';
        case 'shipping':
            return 'Đang giao hàng';
        case 'completed':
            return 'Đã hoàn thành';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return ucfirst($status);
    }
}

function getStatusClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipping':
            return 'primary';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>
