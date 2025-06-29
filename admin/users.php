<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Quản lý người dùng";
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/admin/users.php'));
    exit;
}

// Get all users
$users = getAllUsers();

// Handle filter and sort
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Apply filters
if ($filter === 'admin') {
    $users = array_filter($users, function($user) {
        return $user['role'] === 'admin';
    });
} elseif ($filter === 'customer') {
    $users = array_filter($users, function($user) {
        return $user['role'] === 'customer';
    });
}

// Apply sorting
if ($sort === 'newest') {
    usort($users, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
} elseif ($sort === 'oldest') {
    usort($users, function($a, $b) {
        return strtotime($a['created_at']) - strtotime($b['created_at']);
    });
} elseif ($sort === 'name_asc') {
    usort($users, function($a, $b) {
        return strcmp($a['full_name'], $b['full_name']);
    });
} elseif ($sort === 'name_desc') {
    usort($users, function($a, $b) {
        return strcmp($b['full_name'], $a['full_name']);
    });
}

// Handle user delete
$message = '';
if (isset($_GET['delete']) && $_GET['delete'] != 1) { // Prevent deleting the main admin
    $id = intval($_GET['delete']);

    try {
        // Check if user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            // Delete user
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Người dùng đã được xóa thành công';
        } else {
            $message = 'Không tìm thấy người dùng';
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $message = 'Đã xảy ra lỗi khi xóa người dùng: ' . $e->getMessage();
    }
}

// Search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $users = array_filter($users, function($user) use ($search) {
        return (stripos($user['username'], $search) !== false || 
                stripos($user['email'], $search) !== false || 
                stripos($user['full_name'], $search) !== false);
    });
}

// Handle user role change
if (isset($_POST['update_role']) && !empty($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $newRole = $_POST['role'];

    try {
        // Update user role
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $result = $stmt->execute([$newRole, $userId]);

        if ($result && $stmt->rowCount() > 0) {
            $message = 'Vai trò người dùng đã được cập nhật thành công';
        } else {
            $message = 'Không tìm thấy người dùng hoặc vai trò không thay đổi';
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $message = 'Đã xảy ra lỗi khi cập nhật vai trò người dùng: ' . $e->getMessage();
    }
}

require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản lý /</span> Người dùng
    </h4>

    <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <?= $message ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Filters and search -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <form action="" method="get" class="d-flex gap-2">
              <input type="text" name="search" class="form-control" placeholder="Tìm kiếm người dùng..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-search"></i>
              </button>
            </form>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex gap-2 justify-content-md-end">
              <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bx bx-filter-alt me-1"></i> Lọc
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item <?= $filter === '' ? 'active' : '' ?>" href="?">Tất cả</a></li>
                  <li><a class="dropdown-item <?= $filter === 'admin' ? 'active' : '' ?>" href="?filter=admin">Quản trị viên</a></li>
                  <li><a class="dropdown-item <?= $filter === 'customer' ? 'active' : '' ?>" href="?filter=customer">Khách hàng</a></li>
                </ul>
              </div>
              <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bx bx-sort me-1"></i> Sắp xếp
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>" href="?sort=newest">Mới nhất</a></li>
                  <li><a class="dropdown-item <?= $sort === 'oldest' ? 'active' : '' ?>" href="?sort=oldest">Cũ nhất</a></li>
                  <li><a class="dropdown-item <?= $sort === 'name_asc' ? 'active' : '' ?>" href="?sort=name_asc">Tên A-Z</a></li>
                  <li><a class="dropdown-item <?= $sort === 'name_desc' ? 'active' : '' ?>" href="?sort=name_desc">Tên Z-A</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Users list -->
    <div class="card">
      <div class="card-datatable table-responsive">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
          <?php if (empty($users)): ?>
          <div class="text-center py-5">
            <h4 class="text-muted mb-0">Không tìm thấy người dùng nào</h4>
          </div>
          <?php else: ?>
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tên người dùng</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Ngày đăng ký</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                  <?php if ($user['id'] != 1): ?>
                  <form action="" method="post" class="d-flex">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <select name="role" class="form-select form-select-sm me-2" style="width: 120px;">
                      <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Khách hàng</option>
                      <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                    </select>
                    <button type="submit" name="update_role" class="btn btn-sm btn-primary">
                      <i class="bx bx-save"></i>
                    </button>
                  </form>
                  <?php else: ?>
                  <span class="badge bg-label-primary">Quản trị viên</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                <td>
                  <?php if ($user['id'] != 1): ?>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="if(confirm('Bạn có chắc chắn muốn xóa người dùng này?')) window.location.href='users.php?delete=<?= $user['id'] ?>'">
                        <i class="bx bx-trash me-1"></i> Xóa
                      </a>
                    </div>
                  </div>
                  <?php else: ?>
                  <button class="btn btn-sm btn-icon" disabled>
                    <i class="bx bx-lock-alt"></i>
                  </button>
                  <?php endif; ?>
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

<?php require_once 'layout/footer.php'; ?>
