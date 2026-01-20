# Hướng Dẫn Cài Đặt FoodShop Backend API

## 📋 Yêu Cầu

- Docker Desktop (phiên bản mới nhất)
- Docker Compose
- Git (tùy chọn)

## 🚀 Cài Đặt Nhanh

### Bước 1: Clone hoặc Download Project

```bash
cd /Users/hachinet/Downloads/FoodShop
```

### Bước 2: Khởi động Docker Containers

```bash
docker-compose up -d --build
```

Lệnh này sẽ khởi động các services:
- **app**: PHP 8.2-FPM (Laravel)
- **nginx**: Nginx web server
- **db**: MySQL 8.0
- **redis**: Redis cache
- **phpmyadmin**: phpMyAdmin (quản lý database)

### Bước 3: Đợi MySQL Khởi Động

Đợi khoảng 30 giây để MySQL khởi động hoàn toàn.

### Bước 4: Cài Đặt Laravel

```bash
# Vào container app
docker-compose exec app bash

# Cài đặt Laravel (nếu chưa có)
composer create-project --prefer-dist laravel/laravel:^10.0 temp
mv temp/* temp/.* . 2>/dev/null
rm -rf temp

# Hoặc nếu đã có Laravel, chỉ cài dependencies
composer install

# Copy file .env
cp .env.example .env

# Generate application key
php artisan key:generate

# Cài đặt các package bổ sung
composer require laravel/sanctum
composer require intervention/image
composer require spatie/laravel-permission

# Publish Sanctum config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Thoát container
exit
```

### Bước 5: Cập Nhật File .env

Tạo file `.env` với nội dung:

```env
APP_NAME=FoodShop
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=foodshop_db
DB_USERNAME=foodshop_user
DB_PASSWORD=your_secure_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost:8080,localhost:3000

UPLOAD_MAX_SIZE=10240
IMAGE_MAX_SIZE=5120

VIETCOMBANK_API_URL=https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx

DEFAULT_SEARCH_RADIUS=10
```

### Bước 6: Chạy Migrations và Seeders

```bash
# Chạy migrations
docker-compose exec app php artisan migrate

# Chạy seeders để tạo dữ liệu mẫu
docker-compose exec app php artisan db:seed
```

### Bước 7: Tạo Storage Link

```bash
docker-compose exec app php artisan storage:link
```

### Bước 8: Set Permissions

```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage
docker-compose exec app chmod -R 775 /var/www/bootstrap/cache
```

## ✅ Kiểm Tra

### 1. Kiểm tra API đang chạy

Truy cập: http://localhost:8080/api/test

Bạn sẽ thấy response:
```json
{
  "message": "FoodShop API is running",
  "version": "1.0.0",
  "timestamp": "2024-01-20T10:00:00.000000Z"
}
```

### 2. Kiểm tra phpMyAdmin

Truy cập: http://localhost:8081

- **Server**: db
- **Username**: foodshop_user
- **Password**: (use the password from docker-compose.yml)

### 3. Test Login Admin

```bash
curl -X POST http://localhost:8080/api/auth/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@foodshop.com",
    "password": "YOUR_ADMIN_PASSWORD"
  }'
```

**⚠️ CẢNH BÁO BẢO MẬT**: Thay đổi mật khẩu mặc định ngay sau khi cài đặt!

### 4. Test Login Owner

```bash
curl -X POST http://localhost:8080/api/auth/owner/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "owner@foodshop.com",
    "password": "YOUR_OWNER_PASSWORD"
  }'
```

## 🔧 Các Lệnh Hữu Ích

### Xem logs

```bash
# Xem tất cả logs
docker-compose logs -f

# Xem logs của một service cụ thể
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f nginx
```

### Dừng và Khởi động lại

```bash
# Dừng containers
docker-compose down

# Khởi động lại
docker-compose up -d

# Khởi động lại một service cụ thể
docker-compose restart app
```

### Truy cập vào container

```bash
# Vào container app
docker-compose exec app bash

# Vào MySQL
docker-compose exec db mysql -u foodshop_user -pfoodshop_pass foodshop_db
```

### Clear cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Chạy lại migrations (CẢNH BÁO: Sẽ xóa toàn bộ dữ liệu)

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

## 📊 Database Schema

Sau khi chạy migrations, bạn sẽ có các bảng:

1. **users** - Người dùng (Admin & Restaurant Owner)
2. **countries** - Quốc gia
3. **languages** - Ngôn ngữ
4. **restaurant_types** - Loại nhà hàng
5. **restaurants** - Nhà hàng
6. **food_categories** - Danh mục món ăn
7. **food_category_translations** - Bản dịch danh mục
8. **food_items** - Món ăn
9. **menus** - Menu nhà hàng
10. **news** - Tin tức/Khóa học/Đầu bếp
11. **reviews** - Đánh giá
12. **exchange_rates** - Tỷ giá ngoại hối

## 👤 Tài Khoản Mặc Định

⚠️ **LƯU Ý BẢO MẬT QUAN TRỌNG**: 
- Các tài khoản dưới đây được tạo tự động bởi seeder
- **PHẢI thay đổi mật khẩu ngay sau khi cài đặt**
- Không sử dụng mật khẩu mặc định trong môi trường production

### Admin
- **Email**: admin@foodshop.com
- **Password mặc định**: `admin123` (⚠️ ĐỔI NGAY!)

### Restaurant Owner (Test)
- **Email**: owner@foodshop.com
- **Password mặc định**: `owner123` (⚠️ ĐỔI NGAY!)

## 🌐 API Endpoints

### Authentication
- POST `/api/auth/owner/register` - Đăng ký chủ nhà hàng
- POST `/api/auth/owner/login` - Đăng nhập chủ nhà hàng
- POST `/api/auth/admin/login` - Đăng nhập admin
- POST `/api/auth/logout` - Đăng xuất
- GET `/api/auth/me` - Thông tin người dùng hiện tại

### Restaurants
- GET `/api/restaurants` - Danh sách nhà hàng
- GET `/api/restaurants/search` - Tìm kiếm nhà hàng
- GET `/api/restaurants/nearby` - Nhà hàng gần đây (10km)
- GET `/api/restaurants/{id}` - Chi tiết nhà hàng
- POST `/api/restaurants` - Tạo nhà hàng (Owner)
- PUT `/api/restaurants/{id}` - Cập nhật nhà hàng (Owner)

### Food Items
- GET `/api/food-items` - Danh sách món ăn
- GET `/api/food-items/search` - Tìm kiếm món ăn
- GET `/api/food-items/best-seller` - Món ăn bán chạy
- GET `/api/food-items/{id}` - Chi tiết món ăn
- POST `/api/food-items` - Tạo món ăn (Owner)

### Categories
- GET `/api/food-categories` - Danh sách danh mục
- POST `/api/food-categories` - Tạo danh mục (Admin)
- POST `/api/food-categories/{id}/translations` - Thêm bản dịch (Admin)

### News/Course/Chef
- GET `/api/news` - Danh sách tin tức
- GET `/api/news/by-type/{type}` - Tin tức theo loại (news, course, chef)
- GET `/api/news/{id}` - Chi tiết tin tức
- POST `/api/news` - Tạo tin tức (Admin)

### Admin
- GET `/api/admin/dashboard/stats` - Thống kê
- GET `/api/admin/restaurants` - Danh sách tất cả nhà hàng
- PUT `/api/admin/restaurants/{id}/status` - Cập nhật trạng thái nhà hàng
- PUT `/api/admin/food-items/{id}/status` - Cập nhật trạng thái món ăn

**Xem file README.md để biết danh sách đầy đủ các API endpoints.**

## 🐛 Troubleshooting

### Lỗi: "Connection refused" khi truy cập API

```bash
# Kiểm tra container đang chạy
docker-compose ps

# Khởi động lại containers
docker-compose restart
```

### Lỗi: "SQLSTATE[HY000] [2002] Connection refused"

```bash
# Đợi MySQL khởi động hoàn toàn (khoảng 30 giây)
docker-compose logs -f db

# Khởi động lại app container
docker-compose restart app
```

### Lỗi: "Storage directory is not writable"

```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage
```

### Lỗi: "Class not found"

```bash
# Clear cache và dump autoload
docker-compose exec app composer dump-autoload
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

## 📚 Tài Liệu Bổ Sung

- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum)
- [Docker Documentation](https://docs.docker.com/)

## 🔐 Security Notes

- Đổi mật khẩu admin sau khi cài đặt
- Cập nhật `APP_KEY` trong file `.env`
- Không expose phpMyAdmin trong production
- Sử dụng HTTPS trong production
- Cấu hình CORS phù hợp với frontend domain

## 📞 Hỗ Trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra logs: `docker-compose logs -f`
2. Kiểm tra file `.env`
3. Đảm bảo tất cả containers đang chạy: `docker-compose ps`
