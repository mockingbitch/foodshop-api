# FoodShop API

REST API cho quản lý nhà hàng và món ăn: xác thực (owner/admin), nhà hàng, món ăn, danh mục, tin tức, menu, đánh giá, tỷ giá và dashboard admin. API dùng **JWT** (tymon/jwt-auth).

---

## 📋 Yêu cầu

- **Chạy với Docker:** Docker Desktop, Docker Compose
- **Chạy local:** PHP 8.2+, Composer, MySQL 8.0 (hoặc SQLite để dev)

---

## 🚀 Cài đặt

### Cách 1: Docker (khuyến nghị)

**Bước 1:** Clone / mở thư mục project

```bash
cd /path/to/foodshop-api
```

**Bước 2:** Khởi động containers

```bash
docker-compose up -d --build
```

Các service: **app** (PHP 8.2-FPM + Laravel), **nginx**, **db** (MySQL 8.0), **redis** (tùy chọn), **phpmyadmin** (tùy chọn).

**Bước 3:** Đợi MySQL sẵn sàng (khoảng 30 giây), rồi cài đặt trong container

```bash
docker-compose exec app composer install
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan storage:link
```

**Bước 4:** (Tùy chọn) Phân quyền thư mục

```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

API: **http://localhost:8080** (port map trong `docker-compose.yml`).

---

### Cách 2: Chạy local (không Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
# Cấu hình DB_* trong .env (MySQL/SQLite)
php artisan migrate
php artisan db:seed
php artisan serve
```

API base: **http://localhost:8000/api** (hoặc `APP_URL` + `/api`).

---

## 🔐 Biến môi trường (.env)

Các biến quan trọng:

| Biến | Mô tả |
|------|--------|
| `APP_URL` | URL ứng dụng (vd: http://localhost:8080 hoặc http://localhost:8000) |
| `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Kết nối MySQL |
| `JWT_SECRET` | Tạo bằng lệnh `php artisan jwt:secret` |

Với Docker, `DB_HOST=db`. Với local, `DB_HOST=127.0.0.1`.

**Ảnh upload (URL 404?)**  
Ảnh lưu tại `storage/app/public/uploads/` (restaurant | food | news). Để URL dạng `/storage/uploads/...` hoạt động:

- **Khuyến nghị:** Trên server chạy `php artisan storage:link` để tạo symlink `public/storage` → `storage/app/public`.
- **Nếu không tạo được symlink** (một số hosting): Laravel có route dự phòng trong `routes/web.php` để serve file từ storage khi truy cập `/storage/uploads/...`.

---

## ✅ Kiểm tra sau cài đặt

**1. Health check API**

Mở: `http://localhost:8080/api/test` (Docker) hoặc `http://localhost:8000/api/test` (local).

Response mẫu:

```json
{
  "message": "FoodShop API is running",
  "version": "1.0.0",
  "timestamp": "..."
}
```

**2. Đăng nhập (JWT)**

- Owner: `POST /api/auth/owner/login` với `email`, `password`
- Admin: `POST /api/auth/admin/login`
- Response có `access_token` → gửi kèm header: `Authorization: Bearer {access_token}` cho các API cần đăng nhập.

**3. phpMyAdmin (nếu bật trong Docker)**  
Truy cập port 8081 (xem `docker-compose.yml`), đăng nhập bằng `DB_USERNAME` / `DB_PASSWORD`.

---

## 👤 Tài khoản mặc định (từ Seeder)

⚠️ **Đổi mật khẩu ngay sau lần đăng nhập đầu.**

| Vai trò | Email | Mật khẩu mặc định |
|--------|--------|-------------------|
| Admin | admin@foodshop.com | admin123 |
| Restaurant Owner | owner@foodshop.com | owner123 |

---

## 📚 Tài liệu API

- **Docs tương tác (Scribe):** Mở `/api/docs` trên trình duyệt (có Try it out, Postman, OpenAPI).
- **Tạo lại Scribe:** `php artisan scribe:generate` hoặc `composer docs`.
- **Tham chiếu Markdown:** [docs/api.md](docs/api.md) — danh sách endpoint, method, auth, request/response.
- **Postman:** Import [postman/FoodShop-API.postman_collection.json](postman/FoodShop-API.postman_collection.json). Đặt biến `base_url` và sau khi login điền `token` (Bearer).

---

## 📊 Cấu trúc database (sau migrate)

- users, countries, languages, restaurant_types  
- restaurants, food_categories, food_category_translations  
- food_items, menus, news, reviews  
- exchange_rates, personal_access_tokens (JWT blacklist dùng cache)

Chi tiết bảng xem trong `database/migrations/`.

---

## 🔧 Lệnh hữu ích

**Docker**

```bash
docker-compose logs -f          # Log tất cả
docker-compose logs -f app      # Log app
docker-compose down && docker-compose up -d
docker-compose exec app bash    # Vào shell container app
```

**Artisan (trong container hoặc local)**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan migrate:fresh --seed   # Cảnh báo: xóa dữ liệu và seed lại
```

---

## 🐛 Xử lý lỗi thường gặp

| Lỗi | Gợi ý |
|-----|--------|
| Connection refused (API) | Kiểm tra `docker-compose ps`, `docker-compose restart`. |
| SQLSTATE Connection refused (DB) | Đợi MySQL khởi động xong; với Docker dùng `DB_HOST=db`. |
| Storage not writable | `chmod -R 775 storage bootstrap/cache` (và chown nếu dùng Docker). |
| Class not found | `composer dump-autoload`, `php artisan config:clear`. |
| JWT secret not set | Chạy `php artisan jwt:secret`. |

---

## 🔐 Bảo mật

- Đổi mật khẩu admin/owner mặc định ngay sau cài đặt.
- Không dùng `APP_DEBUG=true` và mật khẩu mặc định trên production.
- Production: dùng HTTPS, cấu hình CORS đúng, không expose phpMyAdmin.

---

## 📄 License

MIT.
