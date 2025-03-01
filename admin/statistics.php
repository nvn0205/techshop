<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pageTitle = "Thống kê";
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . $config['site_url'] . '/login.php?redirect=' . urlencode($config['site_url'] . '/admin/statistics.php'));
    exit;
}

// Lấy tất cả đơn hàng
$orders = getAllOrders();

// Lấy phạm vi ngày từ yêu cầu
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Ngày đầu tháng hiện tại
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); // Hôm nay

// Lọc đơn hàng theo phạm vi ngày
$filteredOrders = array_filter($orders, function($order) use ($startDate, $endDate) {
    $orderDate = date('Y-m-d', strtotime($order['created_at']));
    return $orderDate >= $startDate && $orderDate <= $endDate;
});

// Tính toán thống kê
$totalRevenue = 0;
$totalOrders = count($filteredOrders);
$ordersByStatus = [
    'pending' => 0,
    'processing' => 0,
    'shipping' => 0,
    'completed' => 0,
    'cancelled' => 0
];

foreach ($filteredOrders as $order) {
    if ($order['status'] !== 'cancelled') {
        $totalRevenue += $order['total_amount'];
    }
    $ordersByStatus[$order['status']]++;
}

// Tính doanh thu theo ngày
$dailyRevenue = [];
foreach ($filteredOrders as $order) {
    $orderDate = date('Y-m-d', strtotime($order['created_at']));
    if (!isset($dailyRevenue[$orderDate])) {
        $dailyRevenue[$orderDate] = 0;
    }
    $dailyRevenue[$orderDate] += $order['total_amount'];
}

// Sắp xếp theo ngày tăng dần
ksort($dailyRevenue);

require_once 'layout/header.php';
require_once 'layout/sidebar.php';
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-lg-8 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-7">
              <div class="card-body">
                <h5 class="card-title text-primary">Tổng quan đơn hàng 🎉</h5>
                <p class="mb-4">Trong khoảng thời gian từ <span class="fw-bold"><?= date('d/m/Y', strtotime($startDate)) ?></span> đến <span class="fw-bold"><?= date('d/m/Y', strtotime($endDate)) ?></span></p>

                <a href="orders.php" class="btn btn-sm btn-outline-primary">Xem đơn hàng</a>
              </div>
            </div>
            <div class="col-sm-5 text-center text-sm-left">
              <div class="card-body pb-0 px-0 px-md-4">
                <img src="<?= $config['site_url'] ?>/admin/assets/img/illustrations/man-with-laptop.png" height="140" alt="View Badge User" data-app-dark-img="<?= $config['site_url'] ?>/admin/assets/illustrations/man-with-laptop-dark.png" data-app-light-img="<?= $config['site_url'] ?>/admin/assets/illustrations/man-with-laptop-light.png" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img src="<?= $config['site_url'] ?>/admin/assets/img/icons/unicons/chart.png" alt="chart" class="rounded" />
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Doanh thu</span>
                <h3 class="card-title mb-2"><?= formatPrice($totalRevenue) ?></h3>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img src="<?= $config['site_url'] ?>/admin/assets/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded" />
                  </div>
                </div>
                <span class="fw-semibold d-block mb-1">Đơn hàng</span>
                <h3 class="card-title text-nowrap mb-2"><?= $totalOrders ?></h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Date range filter -->
      <div class="col-12 order-2 mb-4">
        <div class="card">
          <div class="card-body">
            <form method="get" class="row g-3">
              <div class="col-md-5">
                <label class="form-label">Từ ngày</label>
                <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>">
              </div>
              <div class="col-md-5">
                <label class="form-label">Đến ngày</label>
                <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>">
              </div>
              <div class="col-md-2">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                  <i class="bx bx-filter-alt me-1"></i> Lọc
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Order Statistics -->
      <div class="col-md-6 col-lg-4 order-2 mb-4">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
              <h5 class="mb-1 me-2">Thống kê đơn hàng</h5>
              <p class="card-subtitle"><?= $totalOrders ?> tổng đơn hàng</p>
            </div>
            <div class="dropdown">
              <button class="btn text-body-secondary p-0" type="button" id="orderStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderStatistics">
                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                <a class="dropdown-item" href="javascript:void(0);">Share</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex flex-column align-items-center gap-1">
                <h3 class="mb-1"><?= $totalOrders ?></h3>
                <small>Tổng đơn hàng</small>
              </div>
              <div id="orderStatisticsChart"></div>
            </div>
            <ul class="p-0 m-0">
              <li class="d-flex align-items-center mb-4">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-time'></i></i></span>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Chờ xử lý</h6>
                    <small class="text-muted">Đơn hàng mới</small>
                  </div>
                  <div class="user-progress">
                    <h6 class="mb-0"><?= $ordersByStatus['pending'] ?></h6>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center mb-4">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-success"><i class='bx bxs-calendar' ></i></span>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Đang xử lý</h6>
                    <small class="text-muted">Đang chuẩn bị</small>
                  </div>
                  <div class="user-progress">
                    <h6 class="mb-0"><?= $ordersByStatus['processing'] ?></h6>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center mb-4">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-info"><i class='bx bxs-truck' ></i></span>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Đang giao</h6>
                    <small class="text-muted">Đang vận chuyển</small>
                  </div>
                  <div class="user-progress">
                    <h6 class="mb-0"><?= $ordersByStatus['shipping'] ?></h6>
                  </div>
                </div>
              </li>
              <li class="d-flex align-items-center">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-check-circle' ></i></span>
                </div>
                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                  <div class="me-2">
                    <h6 class="mb-0">Hoàn thành</h6>
                    <small class="text-muted">Đã giao</small>
                  </div>
                  <div class="user-progress">
                    <h6 class="mb-0"><?= $ordersByStatus['completed'] ?></h6>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Revenue Report -->
      <div class="col-md-6 col-lg-8 order-2 mb-4">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Báo cáo doanh thu</h5>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="flex-grow-1">
                <h6 class="text-nowrap mb-0">Tổng doanh thu</h6>
                <small class="text-muted"><?= formatPrice($totalRevenue) ?></small>
              </div>
            </div>
            <div id="revenueChart"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'layout/footer.php'; ?>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
// Define chart colors
const cardColor = config.colors_dark ? '#2B2C40' : '#FFFFFF';

// Order Statistics Chart  
const orderChartConfig = {
  chart: {
    height: 165,
    width: 130,
    type: 'donut'
  },
  labels: ['Chờ xử lý', 'Đang xử lý', 'Đang giao', 'Hoàn thành'],
  series: [
    <?= $ordersByStatus['pending'] ?>, 
    <?= $ordersByStatus['processing'] ?>,
    <?= $ordersByStatus['shipping'] ?>,
    <?= $ordersByStatus['completed'] ?>
  ],
  colors: [
    config.colors.primary,
    config.colors.success,
    config.colors.info,
    config.colors.secondary
  ],
  stroke: {
    width: 5,
    colors: cardColor
  },
  dataLabels: {
    enabled: false
  },
  legend: {
    show: false
  },
  grid: {
    padding: {
      top: 0,
      bottom: 0,
      right: 15
    }
  },
  plotOptions: {
    pie: {
      donut: {
        size: '75%',
        labels: {
          show: true,
          name: {
            offsetY: 15
          },
          value: {
            offsetY: -15,
            formatter: function (val) {
              return val;
            }
          },
          total: {
            show: true,
            label: 'Tổng',
            formatter: function (w) {
              return '<?= $totalOrders ?>';
            }
          }
        }
      }
    }
  }
};

const statisticsChart = new ApexCharts(document.querySelector('#orderStatisticsChart'), orderChartConfig);
statisticsChart.render();

// Revenue Chart (Bar chart)
var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
  series: [{
    name: 'Doanh thu',
    data: <?= json_encode(array_values($dailyRevenue)) ?>
  }],
  chart: {
    type: 'line',
    height: 400,
    parentHeightOffset: 0,
    toolbar: {
      show: true,
      tools: {
        download: true,
        selection: true,
        zoom: true,
        zoomin: true,
        zoomout: true,
        pan: true,
        reset: true
      }
    },
    zoom: {
      enabled: true
    }
  },
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: '45%',
      distributed: false,
      endingShape: 'rounded',
      startingShape: 'rounded'
    }
  },
  colors: [config.colors.primary],
  dataLabels: {
    enabled: false
  },
  stroke: {
    width: 3,
    curve: 'smooth',
    lineCap: 'round'
  },
  markers: {
    size: 4,
    colors: config.colors.primary,
    strokeColors: '#fff',
    strokeWidth: 2,
    hover: {
      size: 7,
    }
  },
  legend: {
    show: true,
    position: 'top',
    markers: {
      width: 8,
      height: 8,
      radius: 10
    }
  },
  grid: {
    padding: {
      top: -20
    },
    borderColor: config.colors.borderColor,
    strokeDashArray: 4,
    xaxis: {
      lines: {
        show: true
      }
    }
  },
  xaxis: {
    categories: <?= json_encode(array_keys($dailyRevenue)) ?>,
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    },
    labels: {
      style: {
        colors: config.colors.textMuted,
        fontSize: '13px'
      },
      rotateAlways: true,
      rotate: -45
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: config.colors.textMuted,
        fontSize: '13px'
      },
      formatter: function (value) {
        return new Intl.NumberFormat('vi-VN', { 
          style: 'currency', 
          currency: 'VND',
          maximumFractionDigits: 0
        }).format(value);
      }
    }
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: 'vertical',
      shadeIntensity: 0.3,
      gradientToColors: undefined,
      inverseColors: true,
      opacityFrom: 1,
      opacityTo: 0.6,
      stops: [0, 95, 100]
    }
  },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: function (value) {
        return new Intl.NumberFormat('vi-VN', { 
          style: 'currency', 
          currency: 'VND',
          maximumFractionDigits: 0
        }).format(value);
      }
    }
  }
});

revenueChart.render();
</script>