
<?php
error_reporting(0);
require_once '../config.php';
require_once '../functions.php';



// Get order ID from URL
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($orderId <= 0) {
    die('Invalid order ID');
}

// Get order details
$order = getOrderById($orderId);
if (!$order) {
    die('Order not found');
}
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn hàng #<?= $orderId ?> - <?= $config['site_name'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $config['site_name'] ?></h1>
        <p>Hóa đơn bán hàng</p>
    </div>

    <div class="order-info">
        <p><strong>Mã đơn hàng:</strong> #<?= $order['id'] ?></p>
        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
        <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $index => $item): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= formatPrice($item['price']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="total">Tổng cộng:</td>
                <td><?= formatPrice($order['total_amount']) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="no-print">
        <button onclick="window.print()">In hóa đơn</button>
        <button onclick="window.close()">Đóng</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
