<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
?>




        <!DOCTYPE html>

          <html
            lang="vi"
            class="light-style layout-menu-fixed"
            dir="ltr"
            data-theme="theme-default"
            data-assets-path="<?= $config['site_url'] ?>/admin/assets/"
            data-template="vertical-menu-template-free"
          >
          <head>
            <meta charset="utf-8" />
            <meta
              name="viewport"
              content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
            />

            <title><?= isset($pageTitle) ? $pageTitle . ' - Admin' : 'Admin' ?></title>

            <meta name="description" content="" />

            <!-- Favicon -->
            <link rel="icon" type="image/x-icon" href="<?= $config['site_url'] ?>/admin/assets/img/favicon/favicon.ico" />

            <!-- Fonts -->
            <link rel="preconnect" href="https://fonts.googleapis.com" />
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
            <link
              href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
              rel="stylesheet"
            />

            <!-- Icons. Uncomment required icon fonts -->
            <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />

            <!-- Core CSS -->
            <link rel="stylesheet" href="<?= $config['site_url'] ?>/admin/assets/vendor/css/core.css" class="template-customizer-core-css" />
            <link rel="stylesheet" href="<?= $config['site_url'] ?>/admin/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
            <link rel="stylesheet" href="<?= $config['site_url'] ?>/admin/assets/css/demo.css" />

            <!-- Vendors CSS -->
            <link rel="stylesheet" href="<?= $config['site_url'] ?>/admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

            <!-- Page CSS -->

            <!-- Helpers -->
            <script src="<?= $config['site_url'] ?>/admin/assets/vendor/js/helpers.js"></script>

            <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
            <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
            <script src="<?= $config['site_url'] ?>/admin/assets/js/config.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>



          </head>

          <body>
            <!-- Layout wrapper -->
            <div class="layout-wrapper layout-content-navbar">
              <div class="layout-container">
                <!-- Menu -->

                <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                  <div class="app-brand demo">
                    <a href="<?= $config['site_url'] ?>/admin/" class="app-brand-link">
                      <span class="app-brand-logo demo">
                        <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <defs>
                            <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
                          </defs>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" fill="currentColor" opacity="0.9"></path>
                          </g>
                        </svg>
                      </span>
                      <span class="app-brand-text demo menu-text fw-bolder ms-2"><?= $config['site_name'] ?></span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                      <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                  </div>

                  <div class="menu-inner-shadow"></div>

                  <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                      <a href="<?= $config['site_url'] ?>/admin/" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div data-i18n="Analytics">Trang chủ</div>
                      </a>
                    </li>

                    <!-- Products -->
                    <li class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">
                      <a href="<?= $config['site_url'] ?>/admin/products.php" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-package"></i>
                        <div data-i18n="Products">Sản phẩm</div>
                      </a>
                    </li>

                    <!-- Orders -->
                    <li class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">
                      <a href="<?= $config['site_url'] ?>/admin/orders.php" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cart"></i>
                        <div data-i18n="Orders">Đơn hàng</div>
                      </a>
                    </li>

                    <!-- Users -->
                    <li class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
                      <a href="<?= $config['site_url'] ?>/admin/users.php" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="Users">Người dùng</div>
                      </a>
                    </li>

                    <!-- Statistics -->
                    <li class="menu-item <?= basename($_SERVER['PHP_SELF']) === 'statistics.php' ? 'active' : '' ?>">
                      <a href="<?= $config['site_url'] ?>/admin/statistics.php" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-chart"></i>
                        <div data-i18n="Statistics">Thống kê</div>
                      </a>
                    </li>
                  </ul>
                </aside>
                <!-- / Menu -->

                <!-- Layout container -->
                <div class="layout-page">
                  <!-- Navbar -->

                  <nav
                    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar"
                  >
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="bx bx-menu bx-sm"></i>
                      </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                      <!-- Search -->
                      <div class="navbar-nav align-items-center">
                        <div class="nav-item d-flex align-items-center">
                          <i class="bx bx-search fs-4 lh-0"></i>
                          <input
                            type="text"
                            class="form-control border-0 shadow-none"
                            placeholder="Search..."
                            aria-label="Search..."
                          />
                        </div>
                      </div>
                      <!-- /Search -->

                      <ul class="navbar-nav flex-row align-items-center ms-auto">
                        <!-- Place this tag where you want the button to render. -->


                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                              <img src="<?= $config['site_url'] ?>/admin/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </a>
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                              <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                  <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                      <img src="<?= $config['site_url'] ?>/admin/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                  </div>
                                  <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?= $_SESSION['user']['full_name'] ?></span>
                                    <small class="text-muted">Admin</small>
                                  </div>
                                </div>
                              </a>
                            </li>

                          </ul>
                        </li>
                        <!--/ User -->
                      </ul>
                    </div>
                  </nav>

                  <!-- / Navbar -->