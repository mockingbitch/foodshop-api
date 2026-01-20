# 🔒 Hướng Dẫn Bảo Mật

## ⚠️ QUAN TRỌNG - ĐỌC TRƯỚC KHI TRIỂN KHAI

### 1. Thay Đổi Mật Khẩu Mặc Định

#### Database Password
File `docker-compose.yml` chứa mật khẩu mặc định:
```yaml
MYSQL_ROOT_PASSWORD: root_password  # ⚠️ ĐỔI NGAY!
MYSQL_PASSWORD: foodshop_pass       # ⚠️ ĐỔI NGAY!
```

**Cách thay đổi:**
1. Sửa file `docker-compose.yml`
2. Cập nhật lại file `.env` với mật khẩu mới
3. Rebuild containers: `docker-compose down && docker-compose up -d --build`

#### Admin & Owner Accounts
Các tài khoản được tạo trong `database/seeders/AdminUserSeeder.php`:
- Admin: `admin@foodshop.com` / `admin123`
- Owner: `owner@foodshop.com` / `owner123`

**Cách thay đổi:**
1. Login vào hệ thống với tài khoản admin/owner
2. Sử dụng API để đổi mật khẩu
3. Hoặc chạy lệnh: `php artisan tinker` và update trực tiếp trong database

### 2. Application Key

**KHÔNG BAO GIỜ** commit file `.env` vào git!

File `.env` phải có `APP_KEY` duy nhất:
```bash
php artisan key:generate
```

### 3. Production Checklist

Trước khi deploy production:

- [ ] Đổi tất cả mật khẩu mặc định
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Sử dụng HTTPS (SSL/TLS)
- [ ] Cấu hình CORS phù hợp
- [ ] Tắt phpMyAdmin hoặc bảo vệ bằng authentication
- [ ] Sử dụng mật khẩu database mạnh (>= 16 ký tự)
- [ ] Backup database thường xuyên
- [ ] Cấu hình firewall
- [ ] Enable rate limiting
- [ ] Review tất cả environment variables
- [ ] Xóa tài khoản test

### 4. Bảo Mật API

#### Rate Limiting
API đã được cấu hình rate limiting: 60 requests/phút

Tùy chỉnh trong `app/Providers/RouteServiceProvider.php`:
```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

#### Authentication
Sử dụng Laravel Sanctum để xác thực API:
- Token có thời hạn
- Revoke token khi logout
- Stateful authentication cho SPA

### 5. Database Security

#### Backup
```bash
# Backup database
docker-compose exec db mysqldump -u foodshop_user -p foodshop_db > backup.sql

# Restore database
docker-compose exec -T db mysql -u foodshop_user -p foodshop_db < backup.sql
```

#### Connection Security
- Chỉ cho phép kết nối từ container app
- Không expose MySQL port ra bên ngoài trong production
- Sử dụng SSL cho MySQL connection

### 6. File Upload Security

File upload đã được cấu hình giới hạn:
- Max file size: 5MB per image
- Allowed types: jpeg, png, jpg, webp
- Files được scan và resize trước khi lưu

### 7. Environment Variables

**KHÔNG BAO GIỜ** commit các file sau vào git:
- `.env`
- `.env.production`
- `.env.local`
- `auth.json`

Đã được cấu hình trong `.gitignore`

### 8. Docker Security

#### Production Best Practices
1. Không run containers as root
2. Sử dụng specific image versions (không dùng `latest`)
3. Scan images for vulnerabilities
4. Limit container resources
5. Use secrets management cho sensitive data

### 9. Monitoring & Logging

#### Laravel Logs
Logs được lưu trong `storage/logs/laravel.log`

Xem logs:
```bash
docker-compose exec app tail -f storage/logs/laravel.log
```

#### Docker Logs
```bash
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f nginx
```

### 10. Security Headers

Nginx đã được cấu hình các security headers trong `docker/nginx/default.conf`

Nên thêm:
```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-XSS-Protection "1; mode=block";
add_header X-Content-Type-Options "nosniff";
```

### 11. Incident Response

Nếu phát hiện security breach:

1. **Ngay lập tức:**
   - Tắt hệ thống: `docker-compose down`
   - Thay đổi tất cả passwords
   - Revoke tất cả API tokens

2. **Điều tra:**
   - Check logs: `docker-compose logs`
   - Review database changes
   - Check file modifications

3. **Khôi phục:**
   - Restore từ backup sạch
   - Update security patches
   - Rebuild containers

4. **Báo cáo:**
   - Document incident
   - Notify affected users
   - Update security measures

### 12. Regular Maintenance

Thực hiện định kỳ:

- [ ] Update Laravel & dependencies (monthly)
- [ ] Update Docker images (monthly)
- [ ] Review access logs (weekly)
- [ ] Backup database (daily)
- [ ] Security audit (quarterly)
- [ ] Penetration testing (yearly)

### 13. Contact

Nếu phát hiện lỗ hổng bảo mật, vui lòng báo cáo qua:
- Email: security@yourdomain.com
- Hoặc tạo issue trên repository (cho non-critical issues)

---

**Nhớ rằng**: Security là quá trình liên tục, không phải một lần setup!
