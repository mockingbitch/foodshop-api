# FoodShop API Documentation

Base URL: `{APP_URL}/api`

All requests should include:
- **Content-Type:** `application/json`
- **Accept:** `application/json`
- **Authorization:** `Bearer {token}` (for protected routes)

---

## Authentication

### Register Restaurant Owner
`POST /api/auth/owner/register`

**Body:**
```json
{
  "name": "string (required, max 255)",
  "email": "string (required, email, unique)",
  "password": "string (required, min 8)",
  "password_confirmation": "string (required)",
  "phone": "string (required, max 20)",
  "address": "string (optional)",
  "country_id": "integer (optional, exists:countries)"
}
```

**Response:** `user`, `token` (Bearer), `token_type`

---

### Owner Login
`POST /api/auth/owner/login`

**Body:**
```json
{
  "email": "string (required)",
  "password": "string (required)"
}
```

**Response:** `user`, `token`, `token_type`

---

### Admin Login
`POST /api/auth/admin/login`

**Body:** Same as owner login.

**Response:** `user`, `token`, `token_type`

---

### Logout
`POST /api/auth/logout`  
🔒 **Auth required**

Revokes current access token.

---

### Get Current User (Me)
`GET /api/auth/me`  
🔒 **Auth required**

**Response:** Authenticated user with `country` relation.

---

### Update Owner Profile
`PUT /api/owner/profile`  
🔒 **Auth required (owner)**

**Body:**
```json
{
  "name": "string (optional)",
  "phone": "string (optional)",
  "address": "string (optional)",
  "country_id": "integer (optional)"
}
```

---

## Reference Data

### Languages
| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/languages` | List active languages (sort_order) |
| GET | `/api/languages/{code}` | Get language by code |

### Countries
| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/countries` | List active countries |
| GET | `/api/countries/{id}` | Get country by ID |

### Restaurant Types
| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/restaurant-types` | List active restaurant types |

---

## Restaurants

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/api/restaurants` | No | List active restaurants (paginated). Query: `country_id`, `restaurant_type_id`, `delivery_available`, `search`, `per_page` |
| GET | `/api/restaurants/search` | No | Search by name. Query: `name`, `per_page` |
| GET | `/api/restaurants/nearby` | No | Restaurants within radius. Query: `latitude`, `longitude`, `radius_km` |
| GET | `/api/restaurants/{id}` | No | Restaurant detail with best_sellers, outside_images, inside_images |
| POST | `/api/restaurants` | Owner | Create restaurant (status: pending) |
| PUT | `/api/restaurants/{id}` | Owner/Admin | Update restaurant |
| DELETE | `/api/restaurants/{id}` | Owner/Admin | Delete restaurant |
| GET | `/api/restaurants/{restaurantId}/reviews` | No | Paginated approved reviews |
| POST | `/api/restaurants/{restaurantId}/reviews` | No | Create review (status: pending) |
| GET | `/api/restaurants/{restaurantId}/menus` | No | Active menus for restaurant |

---

## Food Items

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/api/food-items` | No | List active, confirmed-code items. Query: `restaurant_id`, `category_id`, `best_seller`, `vegetarian`, `search`, `per_page` |
| GET | `/api/food-items/search` | No | Search food items |
| GET | `/api/food-items/by-category/{categoryId}` | No | Items by category (paginated) |
| GET | `/api/food-items/best-seller` | No | Best seller items. Query: `restaurant_id`, `per_page` |
| GET | `/api/food-items/{id}` | No | Detail with extra_images, related_products |
| POST | `/api/food-items` | Owner | Create (status: pending, food_code_status: pending) |
| PUT | `/api/food-items/{id}` | Owner/Admin | Update |
| DELETE | `/api/food-items/{id}` | Owner/Admin | Delete |
| POST | `/api/food-items/{id}/confirm-code` | Owner | Confirm food code & set status active |
| GET | `/api/food-items/{foodItemId}/reviews` | No | Paginated approved reviews |
| POST | `/api/food-items/{foodItemId}/reviews` | No | Create review (status: pending) |

---

## Food Categories

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/api/food-categories` | No | List active. Query: `root_only`, `parent_id` |
| GET | `/api/food-categories/{id}` | No | Category with translation & images. Query: `language_code` |
| POST | `/api/food-categories` | Admin | Create with translations |
| PUT | `/api/food-categories/{id}` | Admin | Update |
| DELETE | `/api/food-categories/{id}` | Admin | Delete (fails if has children or food items) |
| POST | `/api/food-categories/{id}/translations` | Admin | Add/update translation |

---

## News / Course / Chef

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/api/news` | No | Published list. Query: `type`, `search`, `per_page` |
| GET | `/api/news/by-type/{type}` | No | Published by type (news, course, chef) |
| GET | `/api/news/{id}` | No | Detail (increments view_count) |
| POST | `/api/news` | Admin | Create |
| PUT | `/api/news/{id}` | Admin | Update |
| DELETE | `/api/news/{id}` | Admin | Delete |

---

## Menus

All menu routes require **auth (owner/admin)**.

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/menus/{id}` | Menu with restaurant |
| POST | `/api/menus` | Create. Body: `restaurant_id`, name, etc. |
| PUT | `/api/menus/{id}` | Update |
| DELETE | `/api/menus/{id}` | Delete |

---

## Exchange Rates

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/api/exchange-rates` | No | Rates for date. Query: `date` (Y-m-d) |
| POST | `/api/exchange-rates/convert` | No | Convert amount. Body: `amount`, `from_currency`, `to_currency`, `date?` |

---

## File Upload

All upload routes require **auth**.

| Method | Path | Description |
|--------|------|-------------|
| POST | `/api/upload/images` | Generic image upload |
| POST | `/api/upload/restaurant-images` | Restaurant images |
| POST | `/api/upload/food-images` | Food item images |

---

## Admin

All admin routes require **auth (admin)**.

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/admin/dashboard/stats` | Dashboard counts (restaurants, food_items, news, users, reviews) |
| GET | `/api/admin/restaurants` | All restaurants. Query: `status`, `per_page` |
| GET | `/api/admin/restaurants/{restaurantId}/food-items` | Restaurant’s food items (paginated) |
| PUT | `/api/admin/restaurants/{id}/status` | Update status (active, hidden, pending) |
| GET | `/api/food-items/pending-codes` | Food items with pending code confirmation |
| GET | `/api/admin/restaurants/{restaurantId}/food-items` | Same as above (AdminFoodItemController) |
| PUT | `/api/admin/food-items/{id}/status` | Update food item status (active, hidden, pending) |

---

## Search (Aliases)

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/search/restaurants` | Same as restaurants/search |
| GET | `/api/search/food-items` | Same as food-items search |
| GET | `/api/search/food-items/by-category` | By category (query params) |
| GET | `/api/search/restaurants/by-distance` | Same as restaurants/nearby |

---

## Health / Test

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/test` | Simple JSON: message, version, timestamp |

---

## Error Responses

- **401 Unauthorized:** Missing or invalid token.
- **403 Forbidden:** Valid token but not allowed (e.g. not owner/admin).
- **404 Not Found:** Resource not found.
- **422 Unprocessable Entity:** Validation errors; body contains `message` and `errors`.

Validation error shape:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```
