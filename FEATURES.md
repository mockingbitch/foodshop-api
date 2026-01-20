# Tính Năng Chi Tiết - FoodShop Backend API

## 📋 Tổng Quan

FoodShop là một hệ thống quản lý nhà hàng và món ăn với các tính năng:
- **Đa ngôn ngữ** (Multilingual support)
- **Quản lý nhà hàng** (Restaurant Management)
- **Quản lý món ăn** (Food Item Management)
- **Hệ thống mã món ăn** (Food Code System)
- **Tích hợp tỷ giá** (Exchange Rate Integration)
- **Tìm kiếm địa lý** (Geolocation Search)

## 🎯 Các Tính Năng Chính

### 1. Authentication & Authorization

#### 1.1 Restaurant Owner
- Đăng ký tài khoản chủ nhà hàng
- Đăng nhập/Đăng xuất
- Quản lý profile cá nhân
- Quản lý nhà hàng của mình
- Thêm và quản lý món ăn

#### 1.2 Admin
- Đăng nhập với quyền admin
- Xem tất cả nhà hàng và món ăn
- Ẩn/hiện nhà hàng và món ăn
- Tạo và quản lý danh mục
- Tạo và quản lý tin tức/khóa học/đầu bếp
- Xác nhận Food Code
- Xem thống kê hệ thống

### 2. Restaurant Management

#### 2.1 Đăng Ký Nhà Hàng
- **Workflow**:
  1. Chọn quốc gia → Tự động hiển thị country code (ccTLD)
  2. Chọn loại nhà hàng (General, Snack Bar, Buffet)
  3. Nhập thông tin đa ngôn ngữ (name, description)
  4. Upload ảnh:
     - Outside: Max 2 ảnh
     - Inside: Max 5 ảnh
  5. Nhập thông tin liên lạc (phone, zalo, email)
  6. Thêm links (Youtube, Facebook, Webpage)
  7. Cấu hình delivery (có/không)
  8. Nhập remark (điều kiện giao hàng, thanh toán - đa ngôn ngữ)
  9. Hệ thống tự động tạo restaurant code

#### 2.2 Restaurant Code
- Format: `{COUNTRY_CODE}-{4_DIGIT_NUMBER}`
- Ví dụ: `VN-0001`, `KR-0002`
- Tự động tăng theo quốc gia

#### 2.3 Tìm Kiếm Nhà Hàng
- **Tìm kiếm theo**:
  - Tên nhà hàng (đa ngôn ngữ)
  - Thành phố
  - Loại nhà hàng
  - Quốc gia
  - Delivery available
  
- **Nearby Search** (Haversine Formula):
  - Tìm nhà hàng trong bán kính (mặc định 10km)
  - Input: latitude, longitude, radius
  - Output: Danh sách nhà hàng + khoảng cách

#### 2.4 Chi Tiết Nhà Hàng
- Thông tin cơ bản
- 2 ảnh outside + 5 ảnh inside
- Menu
- Best seller food items
- Reviews và ratings
- Links mạng xã hội
- Thông tin liên lạc (phone, zalo)

### 3. Food Item Management

#### 3.1 Thêm Món Ăn - Workflow
1. **Chọn ngôn ngữ**: VN, EN, KR, ...
2. **Chọn nhà hàng** (Restaurant Code)
   - Tự động hiển thị:
     - City Name
     - Restaurant Name
3. **Chọn Category** (Food Category)
4. **Nhập tên món ăn** (theo ngôn ngữ đã chọn)
5. **Upload 1 ảnh chính** (main image - bắt buộc)
6. **Upload 5 ảnh phụ** (extra images - tùy chọn)
7. **Nhập thông tin**:
   - Serving size (số phần ăn)
   - Weight (khối lượng - gram)
   - Price (giá - tiền địa phương)
8. **Auto generate Food Code**
9. **USD price tự động tính** (từ exchange rate)
10. **Chờ Manager confirm code**

#### 3.2 Food Code System
- **Format**: `{COUNTRY}-{RESTAURANT_CODE}-{CATEGORY_CODE}-{FOOD_NUMBER}`
- **Ví dụ**: `KR-0001-0102-0001`
  - `KR`: Korea
  - `0001`: Restaurant code
  - `0102`: Category code
  - `0001`: Food item number

- **Workflow**:
  1. Owner tạo món ăn → Food code tự động generate
  2. Status: `pending` (chờ xác nhận)
  3. Manager/Admin xác nhận code
  4. Status: `confirmed` → món ăn active

#### 3.3 Giá Tiền (Price)
- **Input**: Nhập giá theo tiền địa phương (VND, KRW, USD, ...)
- **Auto convert**: Tự động tính giá USD
- **Exchange Rate**: Lấy từ Vietcombank API
- **Display**: Hiển thị cả giá local và USD

#### 3.4 Tìm Kiếm Món Ăn
- **Tìm theo**:
  - Tên món ăn (đa ngôn ngữ)
  - Food Code
  - Category
  - Restaurant
  - Best Seller
  - Vegetarian

#### 3.5 Customer Rating
- Người dùng đánh giá món ăn
- Rating: 1-5 sao
- Comment
- Upload ảnh review (max 5 ảnh)
- Status: pending → approved (admin duyệt)
- Tự động cập nhật:
  - `customer_rating` (trung bình)
  - `customer_review_count` (số lượng)

### 4. Food Category Management

#### 4.1 Category Structure
- **Parent-Child Relationship**:
  - Root categories (parent_id = null)
  - Sub-categories (parent_id = category_id)
  - Unlimited levels

#### 4.2 Multilingual Categories
- **5 images chung** cho tất cả ngôn ngữ
- **Name & Description** theo từng ngôn ngữ
- **Video link** theo từng ngôn ngữ

#### 4.3 Workflow Tạo Category
1. Admin upload 5 images (chung)
2. Nhập category code (4 digits)
3. Chọn parent category (tùy chọn)
4. Click để mở popup/dropdown
5. Chọn language → Nhập name, description, video link
6. Có thể thêm nhiều languages

### 5. News/Course/Chef Module

#### 5.1 Unified Module
- **Gộp chung** 3 loại: News, Course, Chef
- **Phân biệt** bằng field `type`
- **Filter** theo type khi query

#### 5.2 News Type
- Title, content (đa ngôn ngữ)
- Featured image
- Gallery images
- Video link
- Status: published, draft, archived

#### 5.3 Course Type
- Tất cả fields của News +
- Course price
- Course duration (hours)
- Max participants

#### 5.4 Chef Type
- Tất cả fields của News +
- Chef name
- Chef specialty

### 6. Exchange Rate Integration

#### 6.1 Vietcombank API
- Lấy tỷ giá từ Vietcombank
- Currencies: USD, VND, KRW, JPY, CNY, THB, ...
- Auto update daily

#### 6.2 Currency Conversion
- API endpoint: `/api/exchange-rates/convert`
- Input: amount, from_currency, to_currency
- Output: converted_amount
- Sử dụng cho Food Item price

### 7. File Upload

#### 7.1 Image Processing
- **Library**: Intervention Image
- **Resize**: Max width 1200px (keep aspect ratio)
- **Compress**: 85% quality (JPG)
- **Format**: Convert to JPG

#### 7.2 Upload Endpoints
- `/api/upload/images` - General images (max 5)
- `/api/upload/restaurant-images` - Restaurant images (outside 2, inside 5)
- `/api/upload/food-images` - Food images (1 main + 5 extra)

#### 7.3 Storage
- Store: `storage/app/public/`
- Access: `/storage/...`
- Max size: 5MB per image

### 8. Admin Dashboard

#### 8.1 Statistics
- Total restaurants (active, pending, hidden)
- Total food items (active, pending, pending code)
- Total news (published, draft)
- Total users (owners, admins)
- Total reviews (pending, approved)

#### 8.2 Management Functions
- View all restaurants (including hidden)
- Update restaurant status (active/hidden)
- View all food items by restaurant
- Update food item status (active/hidden)
- Confirm food codes
- Approve reviews

### 9. Review System

#### 9.1 Food Item Reviews
- Customer name & email
- Rating (1-5 stars)
- Comment
- Upload images (max 5)
- Status: pending → approved/rejected

#### 9.2 Restaurant Reviews
- Same as Food Item Reviews
- Display on restaurant detail page

#### 9.3 Auto Update Ratings
- When review approved:
  - Recalculate average rating
  - Update review count
  - Update restaurant/food item rating field

### 10. Search & Filter

#### 10.1 Restaurant Search
- By name (multilingual)
- By city
- By category
- By country
- By delivery available
- By distance (nearby)

#### 10.2 Food Item Search
- By name (multilingual)
- By food code
- By category
- By restaurant
- By best seller
- By vegetarian

### 11. Geolocation Features

#### 11.1 Haversine Formula
- Calculate distance between 2 coordinates
- Used for "nearby" search
- Default radius: 10km
- Adjustable radius (1-100km)

#### 11.2 Usage
```php
// Find restaurants within 10km
GET /api/restaurants/nearby?latitude=10.762622&longitude=106.660172&radius=10
```

## 🔒 Security Features

### 1. Authentication
- Laravel Sanctum (API tokens)
- Token-based authentication
- Stateless authentication for API

### 2. Authorization
- Role-based: admin, restaurant_owner
- Middleware protection
- Owner can only manage their own restaurants

### 3. Validation
- Request validation for all inputs
- File validation (type, size)
- Unique constraints (codes, emails)

### 4. Rate Limiting
- API throttle: 60 requests/minute
- Configurable per route

## 📊 Database Design

### Key Features
1. **Soft Deletes**: restaurants, food_items, categories, news
2. **JSON Fields**: Multilingual content
3. **Indexes**: Performance optimization
4. **Foreign Keys**: Data integrity
5. **Polymorphic Relations**: Reviews (restaurant/food items)

### Relationships
- User → Restaurants (1:many)
- Restaurant → FoodItems (1:many)
- FoodCategory → FoodItems (1:many)
- FoodCategory → Translations (1:many)
- FoodCategory → Children (self-referencing)
- Restaurant/FoodItem → Reviews (polymorphic)

## 🌍 Multilingual Support

### Supported Languages
1. English (EN)
2. Vietnamese (VN)
3. Korean (KR)
4. Japanese (JP)
5. Chinese (CN)

### Implementation
- JSON fields for multilingual content
- Separate translation tables for categories
- Language selection in API requests
- Default fallback to English

## 📈 Performance Optimization

1. **Database Indexing**
   - Status fields
   - Foreign keys
   - Search fields
   - Geolocation fields

2. **Caching** (Redis)
   - Session
   - Cache
   - Queue

3. **Eager Loading**
   - Load relationships efficiently
   - Avoid N+1 queries

4. **Pagination**
   - Default: 15 items per page
   - Adjustable via `per_page` parameter

## 🔄 API Response Format

### Success Response
```json
{
  "data": {...},
  "message": "Success message",
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

## 📝 Future Enhancements

1. Real-time notifications
2. Advanced analytics
3. Payment gateway integration
4. Reservation system
5. Loyalty program
6. Mobile app integration
7. AI-powered recommendations
8. Multi-tenant support
