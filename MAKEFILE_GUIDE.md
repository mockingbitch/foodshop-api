# 📖 Hướng Dẫn Sử Dụng Makefile

Tài liệu này hướng dẫn cách sử dụng Makefile để quản lý dự án FoodShop API một cách nhanh chóng và hiệu quả.

## 📋 Mục Lục

- [Giới Thiệu](#giới-thiệu)
- [Cài Đặt](#cài-đặt)
- [Các Lệnh Cơ Bản](#các-lệnh-cơ-bản)
- [Chi Tiết Các Lệnh](#chi-tiết-các-lệnh)
- [Workflow Phổ Biến](#workflow-phổ-biến)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Giới Thiệu

Makefile giúp bạn thực hiện các tác vụ phổ biến trong dự án Laravel mà không cần nhớ các lệnh dài. Thay vì gõ:

```bash
docker-compose exec app php artisan migrate
```

Bạn chỉ cần gõ:

```bash
make migrate
```

### Xem Tất Cả Lệnh Có Sẵn

```bash
make help
```

Lệnh này sẽ hiển thị danh sách tất cả các lệnh có sẵn với mô tả ngắn gọn.

---

## 🚀 Cài Đặt

### Yêu Cầu

- **Windows**: Cài đặt [Make for Windows](https://www.gnu.org/software/make/) hoặc sử dụng [Git Bash](https://git-scm.com/downloads) (đã có sẵn Make)
- **macOS**: Đã có sẵn Make (thông qua Xcode Command Line Tools)
- **Linux**: Đã có sẵn Make

### Kiểm Tra Make Đã Cài Đặt

```bash
make --version
```

Nếu hiển thị version, bạn đã sẵn sàng!

---

## 📚 Các Lệnh Cơ Bản

### 1. Setup Dự Án Lần Đầu

```bash
make setup
```

Lệnh này sẽ tự động:
- Cài đặt composer dependencies
- Tạo file `.env` từ `.env.example`
- Generate application key
- Chạy migrations
- Chạy seeders

**Lưu ý**: Đảm bảo đã cấu hình file `.env` với thông tin database trước khi chạy.

### 2. Khởi Động Docker

```bash
make up
```

Khởi động tất cả Docker containers (app, nginx, db, redis, phpmyadmin).

### 3. Dừng Docker

```bash
make down
```

Dừng tất cả Docker containers.

### 4. Xem Logs

```bash
make logs
```

Xem logs của tất cả containers. Nhấn `Ctrl+C` để thoát.

---

## 📖 Chi Tiết Các Lệnh

### 🛠️ Installation & Setup

#### `make install`
Cài đặt tất cả composer dependencies.

```bash
make install
```

#### `make update`
Cập nhật composer dependencies lên phiên bản mới nhất.

```bash
make update
```

#### `make setup`
Setup hoàn chỉnh dự án (install, env, key, migrate, seed).

```bash
make setup
```

#### `make env`
Tạo file `.env` từ `.env.example` (chỉ khi file `.env` chưa tồn tại).

```bash
make env
```

#### `make key`
Generate application key cho Laravel.

```bash
make key
```

#### `make quick-setup`
Setup nhanh cho developer mới (tương tự `make setup`).

```bash
make quick-setup
```

---

### 🐳 Docker Commands

#### `make up`
Khởi động tất cả Docker containers ở chế độ background.

```bash
make up
```

#### `make down`
Dừng và xóa tất cả Docker containers.

```bash
make down
```

#### `make restart`
Restart tất cả Docker containers.

```bash
make restart
```

#### `make build`
Build lại Docker images.

```bash
make build
```

#### `make rebuild`
Rebuild và restart Docker containers.

```bash
make rebuild
```

#### `make logs`
Xem logs của tất cả containers (theo dõi real-time).

```bash
make logs
```

#### `make logs-app`
Xem logs của app container.

```bash
make logs-app
```

#### `make logs-nginx`
Xem logs của nginx container.

```bash
make logs-nginx
```

#### `make logs-db`
Xem logs của database container.

```bash
make logs-db
```

#### `make shell`
Mở shell trong app container.

```bash
make shell
```

Sau khi vào shell, bạn có thể chạy các lệnh PHP/Artisan trực tiếp.

#### `make shell-db`
Mở MySQL shell để truy vấn database.

```bash
make shell-db
```

---

### 🗄️ Database Commands

#### `make migrate`
Chạy database migrations.

```bash
make migrate
```

#### `make migrate-fresh`
Xóa tất cả bảng và chạy lại migrations từ đầu.

⚠️ **CẢNH BÁO**: Lệnh này sẽ xóa toàn bộ dữ liệu!

```bash
make migrate-fresh
```

#### `make migrate-rollback`
Rollback migration cuối cùng.

```bash
make migrate-rollback
```

#### `make migrate-reset`
Rollback tất cả migrations.

⚠️ **CẢNH BÁO**: Lệnh này sẽ xóa toàn bộ dữ liệu!

```bash
make migrate-reset
```

#### `make seed`
Chạy database seeders để tạo dữ liệu mẫu.

```bash
make seed
```

#### `make fresh`
Xóa tất cả bảng, chạy lại migrations và seed dữ liệu.

⚠️ **CẢNH BÁO**: Lệnh này sẽ xóa toàn bộ dữ liệu!

```bash
make fresh
```

---

### ⚡ Cache & Optimization

#### `make cache-clear`
Xóa tất cả caches (cache, config, route, view).

```bash
make cache-clear
```

Sử dụng khi:
- Thay đổi config nhưng không thấy hiệu ứng
- Thay đổi routes nhưng không thấy route mới
- Gặp lỗi cache cũ

#### `make cache`
Cache configuration, routes và views để tăng hiệu suất.

```bash
make cache
```

#### `make optimize`
Tối ưu hóa ứng dụng (cache config, routes, views).

```bash
make optimize
```

Nên chạy sau khi deploy lên production.

---

### 🧪 Testing

#### `make test`
Chạy PHPUnit tests.

```bash
make test
```

#### `make test-coverage`
Chạy tests với coverage report.

```bash
make test-coverage
```

#### `make pint`
Chạy Laravel Pint để tự động sửa code style.

```bash
make pint
```

#### `make pint-test`
Kiểm tra code style mà không sửa (chỉ báo lỗi).

```bash
make pint-test
```

---

### 🎨 Artisan Commands

#### `make serve`
Khởi động Laravel development server.

```bash
make serve
```

Server sẽ chạy tại: `http://localhost:8000`

#### `make artisan CMD="command"`
Chạy bất kỳ artisan command nào.

**Ví dụ:**

```bash
# List routes
make artisan CMD="route:list"

# Clear cache
make artisan CMD="cache:clear"

# Tạo controller
make artisan CMD="make:controller UserController"
```

#### `make tinker`
Mở Laravel Tinker (REPL).

```bash
make tinker
```

Tinker cho phép bạn tương tác với Laravel application từ command line.

#### `make route-list`
Liệt kê tất cả routes.

```bash
make route-list
```

---

### 📦 Composer Commands

#### `make composer CMD="command"`
Chạy bất kỳ composer command nào.

**Ví dụ:**

```bash
# Cài đặt package
make composer CMD="require intervention/image"

# Xóa package
make composer CMD="remove package-name"

# Cập nhật package
make composer CMD="update package-name"
```

#### `make dump-autoload`
Regenerate composer autoload files.

```bash
make dump-autoload
```

Sử dụng sau khi:
- Thêm/xóa classes
- Thay đổi namespace
- Gặp lỗi "Class not found"

---

### 🛠️ Development Helpers

#### `make make-controller NAME="ControllerName"`
Tạo controller mới.

```bash
make make-controller NAME="UserController"
```

#### `make make-model NAME="ModelName"`
Tạo model mới.

```bash
make make-model NAME="User"
```

#### `make make-migration NAME="migration_name"`
Tạo migration mới.

```bash
make make-migration NAME="create_users_table"
```

#### `make make-seeder NAME="SeederName"`
Tạo seeder mới.

```bash
make make-seeder NAME="UserSeeder"
```

---

### 🚀 Quick Actions

#### `make dev`
Khởi động môi trường development (Docker + clear cache + serve).

```bash
make dev
```

#### `make clean`
Dọn dẹp tất cả caches và file tạm.

```bash
make clean
```

#### `make deploy`
Chạy các lệnh deploy (install, migrate, optimize).

```bash
make deploy
```

---

## 🔄 Workflow Phổ Biến

### Workflow 1: Setup Dự Án Lần Đầu

```bash
# 1. Clone project
git clone <repository-url>
cd foodshop-api

# 2. Cấu hình .env (sửa thông tin database)
# Mở file .env và cập nhật DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Setup hoàn chỉnh
make setup

# 4. Khởi động Docker
make up

# 5. Đợi MySQL khởi động (khoảng 30 giây), sau đó chạy migrations
make migrate

# 6. Seed dữ liệu
make seed
```

### Workflow 2: Làm Việc Hàng Ngày

```bash
# Sáng: Khởi động dự án
make up
make cache-clear

# Tạo migration mới
make make-migration NAME="add_column_to_users"

# Chạy migration
make migrate

# Test code
make test

# Tối: Dừng dự án
make down
```

### Workflow 3: Tạo Feature Mới

```bash
# 1. Tạo model
make make-model NAME="Product"

# 2. Tạo migration
make make-migration NAME="create_products_table"

# 3. Tạo controller
make make-controller NAME="ProductController"

# 4. Chạy migration
make migrate

# 5. Test
make test
```

### Workflow 4: Fix Bug

```bash
# 1. Clear cache (thường là nguyên nhân)
make cache-clear

# 2. Regenerate autoload
make dump-autoload

# 3. Xem logs nếu cần
make logs-app

# 4. Test lại
make test
```

### Workflow 5: Deploy Lên Production

```bash
# 1. Pull code mới nhất
git pull origin main

# 2. Deploy (install, migrate, optimize)
make deploy

# 3. Clear cache
make cache-clear

# 4. Kiểm tra logs
make logs
```

---

## 🐛 Troubleshooting

### Lỗi: "make: command not found"

**Giải pháp:**

- **Windows**: Cài đặt [Make for Windows](https://www.gnu.org/software/make/) hoặc sử dụng Git Bash
- **macOS**: Cài Xcode Command Line Tools: `xcode-select --install`
- **Linux**: `sudo apt-get install make` (Ubuntu/Debian) hoặc `sudo yum install make` (CentOS/RHEL)

### Lỗi: "docker-compose: command not found"

**Giải pháp:**

Cài đặt Docker Desktop (đã bao gồm docker-compose).

### Lỗi: "Connection refused" khi chạy migrate

**Giải pháp:**

```bash
# 1. Kiểm tra Docker đang chạy
make up

# 2. Đợi MySQL khởi động (khoảng 30 giây)
make logs-db

# 3. Thử lại
make migrate
```

### Lỗi: "Class not found"

**Giải pháp:**

```bash
# 1. Regenerate autoload
make dump-autoload

# 2. Clear cache
make cache-clear

# 3. Nếu vẫn lỗi, cài lại dependencies
make install
```

### Lệnh không hoạt động như mong đợi

**Giải pháp:**

1. Kiểm tra bạn đang ở đúng thư mục dự án
2. Kiểm tra Makefile có tồn tại: `ls Makefile`
3. Xem help: `make help`
4. Kiểm tra logs: `make logs`

---

## 💡 Tips & Best Practices

### 1. Luôn Clear Cache Sau Khi Thay Đổi Config

```bash
make cache-clear
```

### 2. Sử Dụng `make help` Để Xem Tất Cả Lệnh

```bash
make help
```

### 3. Kết Hợp Các Lệnh

Bạn có thể kết hợp các lệnh trong Makefile:

```bash
# Ví dụ: Restart và clear cache
make restart && make cache-clear
```

### 4. Sử Dụng Shell Aliases

Thêm vào `~/.bashrc` hoặc `~/.zshrc`:

```bash
alias m='make'
alias ms='make serve'
alias mt='make test'
alias mc='make cache-clear'
```

Sau đó bạn có thể dùng: `m migrate`, `ms`, `mt`, `mc`

### 5. Kiểm Tra Logs Khi Có Vấn Đề

```bash
make logs-app  # Logs của app
make logs-db   # Logs của database
make logs      # Tất cả logs
```

---

## 📝 Ghi Chú

- Tất cả lệnh Makefile đều có thể chạy từ thư mục gốc của dự án
- Một số lệnh yêu cầu Docker đang chạy (`make up` trước)
- Lệnh có `⚠️ CẢNH BÁO` sẽ xóa dữ liệu, cẩn thận khi sử dụng
- Trong môi trường production, nên chạy `make optimize` sau khi deploy

---

## 🔗 Liên Kết Hữu Ích

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Make Documentation](https://www.gnu.org/software/make/manual/)

---

## ❓ Câu Hỏi Thường Gặp

### Q: Tôi có thể tạo lệnh Makefile tùy chỉnh không?

A: Có! Mở file `Makefile` và thêm lệnh mới theo format:

```makefile
my-command: ## Mô tả lệnh
	@echo "Running my command..."
	php artisan my:command
```

### Q: Làm sao để chạy lệnh trong Docker container?

A: Hầu hết lệnh đã tự động chạy trong container. Nếu cần chạy thủ công:

```bash
make shell
# Sau đó chạy lệnh trong shell
```

### Q: Tôi có thể sử dụng Makefile trên Windows không?

A: Có! Sử dụng Git Bash hoặc cài Make for Windows. WSL (Windows Subsystem for Linux) cũng hỗ trợ tốt.

---

**Chúc bạn code vui vẻ! 🚀**
