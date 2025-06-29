# 🍎 TechShop - Website Bán Hàng Công Nghệ

Website bán hàng công nghệ xây dựng bằng PHP

![techshop](https://img.shields.io/badge/techshop-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

## 📋 Mục lục

- [Tính năng chính](#-tính-năng-chính)
- [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
- [Cài đặt](#-cài-đặt)
- [Cấu trúc project](#-cấu-trúc-project)
- [Database Schema](#-database-schema)
- [API Endpoints](#-api-endpoints)
- [Tính năng chi tiết](#-tính-năng-chi-tiết)
- [Screenshots](#-screenshots)
- [Đóng góp](#-đóng-góp)
- [License](#-license)

## ✨ Tính năng chính

### 🛍️ Frontend (Trang khách hàng)
- **Trang chủ đẹp mắt** với banner slider và hiển thị sản phẩm theo danh mục
- **Quản lý sản phẩm** - Xem danh sách, tìm kiếm, lọc theo danh mục/giá/trạng thái
- **Giỏ hàng thông minh** - Thêm/xóa/cập nhật số lượng sản phẩm
- **Hệ thống đặt hàng** - Checkout với thông tin giao hàng và thanh toán
- **Tài khoản người dùng** - Đăng ký, đăng nhập, quản lý thông tin cá nhân
- **Theo dõi đơn hàng** - Xem lịch sử và trạng thái đơn hàng
- **Responsive design** - Tương thích mọi thiết bị

### 🔧 Admin Dashboard
- **Dashboard tổng quan** - Thống kê doanh thu, đơn hàng, sản phẩm
- **Quản lý người dùng** - CRUD users, phân quyền admin/customer
- **Quản lý sản phẩm** - Thêm/sửa/xóa sản phẩm với nhiều ảnh
- **Quản lý đơn hàng** - Cập nhật trạng thái, in đơn hàng, tìm kiếm
- **Thống kê nâng cao** - Biểu đồ doanh thu, báo cáo theo thời gian
- **Giao diện admin hiện đại** - Sử dụng Sneat Bootstrap template

## 🛠️ Công nghệ sử dụng

### Backend
- **PHP 8.2** - Ngôn ngữ lập trình chính
- **MySQL 5.7+** - Hệ quản trị cơ sở dữ liệu
- **PDO/MySQLi** - Kết nối database

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **Sneat Admin Template** - Giao diện admin
- **JavaScript/jQuery** - Tương tác client-side
- **ApexCharts** - Biểu đồ thống kê
- **Font Awesome & BoxIcons** - Icon library

### Tính năng khác
- **Session Management** - Quản lý phiên đăng nhập
- **Password Hashing** - Bảo mật mật khẩu với bcrypt
- **File Upload** - Upload ảnh sản phẩm
- **Search & Filter** - Tìm kiếm và lọc nâng cao
- **Pagination** - Phân trang
- **Responsive Design** - Tương thích mobile

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP 8.2 trở lên
- MySQL 5.7 trở lên
- Apache/Nginx web server
- Composer (khuyến nghị)

### Bước 1: Clone repository
```bash
git clone https://github.com/your-username/techshop.git
cd techshop
```

### Bước 2: Cấu hình database
1. Tạo database mới:
```sql
CREATE DATABASE techshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import database schema:
```bash
mysql -u username -p techshop < techshop.sql
```

### Bước 3: Cấu hình kết nối
1. Copy file config mẫu:
```bash
cp config.example.php config.php
```

2. Chỉnh sửa file `config.php`:
```php
$db_config = [
    'host' => 'localhost',
    'name' => 'techshop',
    'user' => 'your_username',
    'pass' => 'your_password'
];
```

### Bước 4: Cấu hình web server
#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Bước 5: Phân quyền thư mục
```bash
chmod 755 uploads/
chmod 644 config.php
```

### Bước 6: Truy cập website
- **Frontend**: `http://localhost`
- **Admin**: `http://localhost/admin/`
  - Username: `admin`
  - Password: `admin123`

## 📁 Cấu trúc project

```
techshop/
├── admin/                    # Trang quản trị
│   ├── assets/              # Resources cho admin
│   │   ├── css/            # Stylesheets
│   │   ├── js/             # JavaScript files
│   │   ├── img/            # Images & icons
│   │   └── vendor/         # Third-party libraries
│   ├── layout/             # Layout components
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── index.php           # Dashboard chính
│   ├── products.php        # Quản lý sản phẩm
│   ├── orders.php          # Quản lý đơn hàng
│   ├── users.php           # Quản lý người dùng
│   ├── statistics.php      # Thống kê
│   └── *.php              # Các file chức năng khác
├── assets/                 # Resources cho frontend
│   ├── css/
│   ├── js/
│   └── img/
├── uploads/               # Thư mục upload ảnh
├── config.php            # Cấu hình chính
├── functions.php         # Các hàm chung
├── index.php            # Trang chủ
├── product.php          # Chi tiết sản phẩm
├── cart.php             # Giỏ hàng
├── checkout.php         # Thanh toán
├── login.php            # Đăng nhập
├── register.php         # Đăng ký
├── search.php           # Tìm kiếm
├── category.php         # Danh mục sản phẩm
├── my-orders.php        # Đơn hàng của tôi
├── track-order.php      # Theo dõi đơn hàng
├── header.php           # Header frontend
├── footer.php           # Footer frontend
├── techshop.sql         # Database schema
└── README.md           # Tài liệu này
```

## 🗄️ Database Schema

### Bảng `users`
```sql
- id (INT, PRIMARY KEY)
- username (VARCHAR(50), UNIQUE)
- password (VARCHAR(255))
- email (VARCHAR(100), UNIQUE)
- full_name (VARCHAR(100))
- role (ENUM: 'admin', 'customer')
- phone (VARCHAR(20))
- address (TEXT)
- created_at (TIMESTAMP)
- updated_at (DATETIME)
```

### Bảng `categories`
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR(200))
- slug (VARCHAR(100), UNIQUE)
- description (TEXT)
- image (VARCHAR(200))
- parent_id (INT, NULL)
- created_at (TIMESTAMP)
- updated_at (DATETIME)
```

### Bảng `products`
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR(200))
- category_id (INT, FOREIGN KEY)
- price (DECIMAL(12,2))
- sale_price (DECIMAL(12,2))
- description (TEXT)
- features (TEXT)
- stock (INT)
- sku (VARCHAR(50))
- images (TEXT, JSON)
- discount_percent (INT)
- status (ENUM: 'active', 'inactive')
- created_at (TIMESTAMP)
- updated_at (DATETIME)
```

### Bảng `orders`
```sql
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY, NULL)
- total_amount (DECIMAL(12,2))
- status (VARCHAR(32))
- payment_method (VARCHAR(50))
- shipping_address (TEXT)
- phone (VARCHAR(20))
- notes (TEXT)
- full_name (VARCHAR(100))
- email (VARCHAR(100))
- created_at (TIMESTAMP)
- updated_at (DATETIME)
```

### Bảng `order_items`
```sql
- id (INT, PRIMARY KEY)
- order_id (INT, FOREIGN KEY)
- product_id (INT, FOREIGN KEY)
- quantity (INT)
- price (DECIMAL(12,2))
- created_at (TIMESTAMP)
```

## 🔌 API Endpoints

### Frontend Routes
- `GET /` - Trang chủ
- `GET /product.php?id={id}` - Chi tiết sản phẩm
- `GET /category.php?slug={slug}` - Danh mục sản phẩm
- `GET /search.php?q={keyword}` - Tìm kiếm sản phẩm
- `GET /cart.php` - Giỏ hàng
- `GET /checkout.php` - Thanh toán
- `GET /login.php` - Đăng nhập
- `GET /register.php` - Đăng ký
- `GET /my-orders.php` - Đơn hàng của tôi
- `GET /track-order.php` - Theo dõi đơn hàng

### Admin Routes
- `GET /admin/` - Dashboard
- `GET /admin/products.php` - Quản lý sản phẩm
- `GET /admin/orders.php` - Quản lý đơn hàng
- `GET /admin/users.php` - Quản lý người dùng
- `GET /admin/statistics.php` - Thống kê

## 📊 Tính năng chi tiết

### 🛒 Giỏ hàng & Đặt hàng
- **Session-based cart** - Giỏ hàng lưu trong session
- **Real-time updates** - Cập nhật số lượng real-time
- **Multiple payment methods** - COD, Bank transfer
- **Order tracking** - Theo dõi trạng thái đơn hàng
- **Email notifications** - Thông báo qua email

### 🔍 Tìm kiếm & Lọc
- **Full-text search** - Tìm kiếm theo tên và mô tả
- **Advanced filters** - Lọc theo danh mục, giá, trạng thái
- **Sorting options** - Sắp xếp theo giá, tên, ngày
- **Pagination** - Phân trang kết quả

### 📈 Thống kê & Báo cáo
- **Revenue charts** - Biểu đồ doanh thu theo thời gian
- **Order statistics** - Thống kê đơn hàng theo trạng thái
- **Product analytics** - Sản phẩm bán chạy, tồn kho
- **Date range filters** - Lọc theo khoảng thời gian

### 🔐 Bảo mật
- **Password hashing** - Mã hóa mật khẩu với bcrypt
- **Session management** - Quản lý phiên đăng nhập an toàn
- **SQL injection prevention** - Sử dụng prepared statements
- **XSS protection** - Lọc dữ liệu đầu vào
- **CSRF protection** - Bảo vệ chống tấn công CSRF

## 📱 Screenshots

### Frontend
- Trang chủ với banner slider và sản phẩm nổi bật
![Trang chủ](https://i.imgur.com/p3YtVic.png)
- Danh sách sản phẩm với bộ lọc và tìm kiếm
![Danh sách sản phẩm](https://i.imgur.com/e7zwq5F.png)
- Chi tiết sản phẩm với gallery ảnh
![Chi tiết sản phẩm](https://i.imgur.com/LwwlMmy.png)
- Giỏ hàng và checkout
![Giỏ hàng](https://i.imgur.com/YCL0CDN.png)
![Trang thanh toán](https://i.imgur.com/YkO1ndW.png)
- Trang đăng nhập/đăng ký
![Trang đăng nhập](https://i.imgur.com/YkO1ndW.png)
![Trang đăng ký](https://i.imgur.com/id9U82Q.png)

### Admin Dashboard
- Dashboard tổng quan với thống kê
![Dashboard](https://i.imgur.com/uyPI1Eb.png)
- Quản lý sản phẩm với CRUD operations
![Quản lý sản phẩm](https://i.imgur.com/ykzV0FY.png)
- Quản lý đơn hàng với cập nhật trạng thái
![Quản lý đơn hàng](https://i.imgur.com/8NyRsNj.png)
- Thống kê với biểu đồ ApexCharts
![Thống kê](https://i.imgur.com/Q2dNrbT.png)
- Quản lý người dùng
![Quản lý người dùng](https://i.imgur.com/p78TcR0.png)

## 🤝 Đóng góp

Chúng tôi rất hoan nghênh mọi đóng góp! Hãy:

1. Fork project này
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

### Guidelines
- Tuân thủ coding standards
- Viết comments cho code phức tạp
- Test kỹ trước khi submit
- Cập nhật documentation nếu cần

## 📄 License

Project này được phân phối dưới giấy phép MIT. Xem file `LICENSE` để biết thêm chi tiết.

## 📞 Liên hệ

- **Email**: nongvannguyen2004@gmail.com
- **Phone**: 0333532004

---

**Made with ❤️ by NVN** 🍎
