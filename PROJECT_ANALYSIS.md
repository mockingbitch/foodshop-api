# Phân Tích Số Lượng Màn Hình Frontend & API Backend
## Food Project - Core Requirements Analysis

---

## 📊 Tổng Quan

| Loại | Số Lượng | Ghi Chú |
|------|----------|---------|
| **Frontend Pages** | 24 pages | Public: 12, Owner: 5, Admin: 7 |
| **API Endpoints** | 56 endpoints | 12 controllers |
| **React Components** | 41 components | Reusable components |

---

## 🖥️ Frontend Pages (24 pages)

### Public Pages (12 pages)
- `/` - Home Page
- `/restaurants` - Restaurant List
- `/restaurants/search` - Restaurant Search (địa chỉ + tên món ăn → show nhà hàng trong 10km, có label category)
- `/restaurants/:id` - Restaurant Detail
- `/restaurants/:id/menu` - Restaurant Menu
- `/food-items` - Food Listing (text search theo tên món ăn)
- `/food-items/:id` - Food Detail
- `/food-categories` - Food Category List
- `/food-categories/:id` - Food Category Detail
- `/news` - News/Course/Chef List (filter by type)
- `/news/:id` - News/Course/Chef Detail
- `/owner/register`, `/owner/login` - Owner Register/Login

### Owner Pages (5 pages)
- `/owner/profile` - Owner Profile
- `/owner/restaurant/register` - Restaurant Registration
- `/owner/restaurant/:id/edit` - Restaurant Edit
- `/owner/food-items/create` - Food Item Create
- `/owner/food-items/:id/edit` - Food Item Edit

### Admin Pages (7 pages)
- `/admin/login` - Admin Login
- `/admin/dashboard` - Admin Dashboard
- `/admin/restaurants` - Admin Restaurant List (text search, edit status)
- `/admin/restaurants/:id/food-items` - Admin Food Items List (text search, edit status)
- `/admin/categories` - Category Management
- `/admin/categories/create` - Category Create (5 images chung → popup/dropdown multilingual)
- `/admin/categories/:id/edit` - Category Edit
- `/admin/news/create` - News/Course/Chef Create

---

## 🔌 Backend API Endpoints (56 endpoints)

### 1. Restaurant APIs (9 endpoints)
- `GET /api/restaurants` - List nhà hàng (status = 1)
- `GET /api/restaurants/search` - Search: địa chỉ + tên món ăn → nhà hàng trong 10km
- `GET /api/restaurants/by-category/{categoryId}` - Tìm nhà hàng theo category món ăn (click label category)
- `GET /api/restaurants/{id}` - Detail nhà hàng
- `POST /api/restaurants` - Owner tạo nhà hàng
- `PUT /api/restaurants/{id}` - Owner cập nhật
- `DELETE /api/restaurants/{id}` - Owner xóa
- `GET /api/admin/restaurants` - Admin list (text search, kể cả status = 0)
- `PUT /api/admin/restaurants/{id}/status` - Admin edit status (0/1)

**Fields:** `phone`, `zalo`, `delivery_available`, `status` (0=hidden, 1=active), `code`, `remark` (multilingual), `restaurant_type_id`, images (Outside 2, Inside 5), links (Youtube, Facebook, Webpage)

### 2. Food Item APIs (9 endpoints)
- `GET /api/food-items` - List món ăn (status = 1)
- `GET /api/food-items/search` - Text search theo tên món ăn
- `GET /api/food-items/by-category/{categoryId}` - Món ăn theo category
- `GET /api/food-items/{id}` - Detail món ăn
- `POST /api/food-items` - Owner tạo món ăn (status = 0)
- `PUT /api/food-items/{id}` - Owner cập nhật
- `DELETE /api/food-items/{id}` - Owner xóa
- `GET /api/admin/restaurants/{restaurantId}/food-items` - Admin list món ăn (text search, kể cả status = 0)
- `PUT /api/admin/food-items/{id}/status` - Admin edit status (0/1)

**Fields:** `status` (0=hidden, 1=active), `food_code` (auto: KR-0001-0102), `main_image` (1 ảnh), `extra_images` (5 ảnh), `price_usd` (auto convert từ Vietcombank API)

### 3. Food Category APIs (6 endpoints)
- `GET /api/food-categories` - List categories (có parent)
- `GET /api/food-categories/{id}` - Detail category
- `POST /api/food-categories` - Admin tạo (5 images chung, multilingual)
- `PUT /api/food-categories/{id}` - Admin cập nhật
- `DELETE /api/food-categories/{id}` - Admin xóa
- `POST /api/food-categories/{id}/translations` - Thêm/sửa translation

**Workflow:** Chọn 5 images (chung) → popup/dropdown → chọn language → nhập name/description/video_link

### 4. News/Course/Chef APIs (6 endpoints)
- `GET /api/news` - List tin tức (filter by type: news/course/chef)
- `GET /api/news/by-type/{type}` - Filter theo type
- `GET /api/news/{id}` - Detail tin tức
- `POST /api/news` - Admin tạo (chọn type)
- `PUT /api/news/{id}` - Cập nhật
- `DELETE /api/news/{id}` - Xóa

**Note:** Gộp chung, dùng field `type` để phân biệt

### 5. Menu APIs (5 endpoints)
- `GET /api/restaurants/{restaurantId}/menus` - Menu nhà hàng
- `GET /api/menus/{id}` - Detail menu
- `POST /api/menus` - Tạo menu
- `PUT /api/menus/{id}` - Cập nhật
- `DELETE /api/menus/{id}` - Xóa

### 6. Auth APIs (6 endpoints)
- `POST /api/owner/register` - Owner đăng ký (status = 1 luôn)
- `POST /api/owner/login` - Owner login
- `POST /api/admin/login` - Admin login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - User info
- `PUT /api/owner/profile` - Update profile

### 7. Admin APIs (3 endpoints)
- `GET /api/admin/dashboard/stats` - Statistics
- `GET /api/admin/restaurants/{id}/food-items` - List món ăn (text search)
- `PUT /api/admin/food-items/{id}/status` - Edit status món ăn

### 8. File Upload APIs (3 endpoints)
- `POST /api/upload/images` - Upload multiple images
- `POST /api/upload/restaurant-images` - Upload restaurant images (Outside 2, Inside 5)
- `POST /api/upload/food-images` - Upload food image (1 main + 5 extra)

### 9. Review APIs (2 endpoints)
- `GET /api/food-items/{foodItemId}/reviews` - Reviews món ăn
- `POST /api/food-items/{foodItemId}/reviews` - Tạo review món ăn

### 10. Language/Country/RestaurantType APIs (5 endpoints)
- `GET /api/languages` - List languages (nhập tay, không CRUD)
- `GET /api/countries` - List countries (nhập tay, không CRUD)
- `GET /api/restaurant-types` - List types: General, Snack Bar, Buffet (nhập tay, không CRUD)

### 11. Exchange Rate APIs (1 endpoint)
- `GET /api/exchange-rates` - Vietcombank exchange rates (USD → VND)

---

## 📋 Yêu Cầu Chính

### 1. News/Course/Chef Gộp Chung
- Gộp thành 1 module với field `type` (news, course, chef)
- Admin quản lý, không cần view riêng

### 2. Không Có Shopping Cart
- Loại bỏ Cart, Checkout, Order
- Khách hàng liên lạc trực tiếp qua phone/zalo

### 3. Restaurant Search
- 2 ô input: địa chỉ + tên món ăn
- Show nhà hàng có món ăn đó (trong 10km)
- Click nhà hàng → show món ăn
- Label category món ăn (click → show nhà hàng)
- Chỉ có ở front-end

### 4. User Management
- Chỉ có Owner và Admin
- Owner đăng ký → status = 1 (active) luôn
- Không có customer/student

### 5. Status Management
- Restaurant & Food Item: `status` (0=hidden, 1=active)
- Owner thêm → status = 0
- Admin edit → status = 1 để show ra front-end
- Admin có text search trong listing

### 6. Category Workflow
- 5 images chung cho tất cả languages
- Popup/dropdown để nhập name/description theo từng language
- Có parent category (self-referencing)

### 7. Restaurant Entry
- Chọn country → auto show code (ccTLDs)
- Chọn type (General, Snack Bar, Buffet)
- Images: Outside 2, Inside 5
- Links: Youtube, Facebook, Webpage
- Remark: text description (multilingual)

### 8. Food Item Entry
- Workflow: Chọn ngôn ngữ → Chọn nhà hàng (auto show city/name) → Category → 1 ảnh chính → Price (USD auto convert từ Vietcombank) → Food Code auto generate (KR-0001-0102)
- Status = 0 khi owner thêm
- Admin edit status = 1 để show

### 9. Language/Country/Type
- Nhập tay vào DB (dùng script)
- Chỉ có GET APIs, không CRUD
- Field `code`: VN, US, KR...

### 10. Food Listing
- Chỉ text search theo tên món ăn
- Không có filters (best seller, category, vegetarian)

---

## 📊 Tổng Kết

| Controller | Endpoints | Chức Năng |
|------------|-----------|-----------|
| `RestaurantController` | 9 | CRUD + search (địa chỉ + tên món ăn) + by category + admin status |
| `FoodItemController` | 9 | CRUD + text search + admin status |
| `FoodCategoryController` | 6 | CRUD + multilingual + parent category |
| `NewsController` | 6 | CRUD news/course/chef (gộp chung) |
| `MenuController` | 5 | CRUD menu items |
| `AuthController` | 6 | Owner/Admin auth |
| `AdminController` | 3 | Dashboard + food items management |
| `FileUploadController` | 3 | Upload images |
| `ReviewController` | 2 | Reviews món ăn (không review nhà hàng) |
| `LanguageController` | 2 | GET languages |
| `CountryController` | 2 | GET countries |
| `RestaurantTypeController` | 1 | GET types |
| `ExchangeRateController` | 1 | Vietcombank rates |
| **TỔNG** | **56 endpoints** | **12 controllers** |

---