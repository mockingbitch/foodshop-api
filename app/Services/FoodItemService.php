<?php

namespace App\Services;

use App\Contracts\Repositories\ExchangeRateRepositoryInterface;
use App\Contracts\Repositories\FoodCategoryRepositoryInterface;
use App\Contracts\Repositories\FoodItemRepositoryInterface;
use App\Contracts\Repositories\RestaurantRepositoryInterface;
use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Food item business logic: list, search, by category, best seller, show, store, update, destroy,
 * confirm code, pending codes; admin update status and list by restaurant.
 */
class FoodItemService
{
    public function __construct(
        protected FoodItemRepositoryInterface $foodItemRepository,
        protected RestaurantRepositoryInterface $restaurantRepository,
        protected FoodCategoryRepositoryInterface $foodCategoryRepository,
        protected ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {}

    /**
     * List of active food items with confirmed code and optional filters. Paginated unless per_page=all.
     * When group_by=category, returns all items grouped by category (per_page ignored).
     *
     * @param  array  $filters  restaurant_id?, category_id?, best_seller?, vegetarian?, search?, per_page?, group_by? ('category')
     */
    public function index(array $filters): LengthAwarePaginator|Collection|array
    {
        if (! empty($filters['group_by']) && $filters['group_by'] === 'category') {
            return $this->indexGroupedByCategory($filters);
        }

        return $this->foodItemRepository->getActiveConfirmedPaginated($filters);
    }

    /**
     * Get food items grouped by category. Uses per_page=all internally.
     *
     * @return array{grouped: true, data: array<int, array{category: FoodCategory, items: array}>, total: int}
     */
    private function indexGroupedByCategory(array $filters): array
    {
        $filtersForAll = array_merge($filters, ['per_page' => 'all']);
        unset($filtersForAll['group_by']);
        $collection = $this->foodItemRepository->getActiveConfirmedPaginated($filtersForAll);
        if (! $collection instanceof Collection) {
            $collection = $collection->getCollection();
        }
        $grouped = $collection->groupBy('food_category_id');
        $categoryIds = $grouped->keys()->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->unique()->values()->all();
        if (empty($categoryIds)) {
            return ['grouped' => true, 'data' => [], 'total' => 0];
        }
        $categories = FoodCategory::with('translations')->whereIn('id', $categoryIds)->orderBy('sort_order')->get();
        $data = $categories->map(fn (FoodCategory $cat) => [
            'category' => $cat,
            'items' => $grouped->get((string) $cat->id, collect())->values()->all(),
        ])->values()->all();

        return ['grouped' => true, 'data' => $data, 'total' => $collection->count()];
    }

    /**
     * Get food items by category ID.
     */
    public function getByCategory(int $categoryId): LengthAwarePaginator
    {
        return $this->foodItemRepository->getByCategory($categoryId);
    }

    /**
     * Get best seller food items with optional restaurant filter. Paginated unless per_page=all.
     *
     * @param  array  $filters  restaurant_id?, per_page? (int or 'all')
     */
    public function getBestSeller(array $filters): LengthAwarePaginator|Collection
    {
        return $this->foodItemRepository->getBestSellerPaginated($filters);
    }

    /**
     * Get food item detail with related products and extra images.
     *
     * @return array{food_item: FoodItem, extra_images: array, related_products: Collection}
     */
    public function show(int $id): array
    {
        $foodItem = $this->foodItemRepository->findWithRelations($id);

        $relatedProducts = $this->foodItemRepository->getRelatedByCategory(
            $foodItem->food_category_id,
            $foodItem->id
        );

        return [
            'food_item' => $foodItem,
            'extra_images' => $foodItem->getExtraImages(),
            'related_products' => $relatedProducts,
        ];
    }

    /**
     * Create food item (owner). Checks ownership; generates food code; converts price to USD. Status pending.
     *
     * @param  array  $data  Validated store data
     *
     * @throws AuthorizationException
     */
    public function store(User $user, array $data): FoodItem
    {
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name'] = $this->normalizeFoodItemNameKeys($data['name']);
        }

        $data = $this->sanitizeDescription($data);
        $restaurant = $this->restaurantRepository->findOrFail($data['restaurant_id'])->load('country');

        if ($restaurant->user_id !== $user->id && ! $user->isAdmin()) {
            Log::warning('Food item create unauthorized', ['restaurant_id' => $data['restaurant_id'], 'user_id' => $user->id]);
            throw new AuthorizationException('Unauthorized');
        }

        $foodCode = $this->generateFoodCode(
            $restaurant->country->code,
            $restaurant->code,
            $data['food_category_id']
        );

        $priceUsd = $this->convertToUSD($data['price'], $data['currency_code']);

        $foodItem = $this->foodItemRepository->create(array_merge($data, [
            'food_code' => $foodCode,
            'food_code_status' => 'confirmed',
            'price_usd' => $priceUsd,
            'status' => 'active',
        ]));

        Log::info('Food item created', ['food_item_id' => $foodItem->id, 'restaurant_id' => $data['restaurant_id'], 'food_code' => $foodCode]);

        return $foodItem->load(['restaurant', 'foodCategory']);
    }

    /**
     * Update food item. Throws AuthorizationException if user is not owner or admin. Recalculates price_usd if price/currency sent.
     *
     * @throws AuthorizationException
     */
    public function update(User $user, int $id, array $data): FoodItem
    {
        $foodItem = $this->foodItemRepository->findWithRelations($id);

        if ($foodItem->restaurant->user_id !== $user->id && ! $user->isAdmin()) {
            Log::warning('Food item update unauthorized', ['food_item_id' => $id, 'user_id' => $user->id]);
            throw new AuthorizationException('Unauthorized');
        }

        if (array_key_exists('name', $data) && is_array($data['name'])) {
            $existing = is_array($foodItem->name) ? $foodItem->name : [];
            $incoming = $this->normalizeFoodItemNameKeys($data['name']);
            $data['name'] = array_merge($existing, $incoming);
        }

        $regeneratedFoodCode = null;
        if (array_key_exists('restaurant_id', $data)
            && (int) $data['restaurant_id'] !== (int) $foodItem->restaurant_id) {
            $targetRestaurant = $this->restaurantRepository->findWithRelations((int) $data['restaurant_id']);
            if ($targetRestaurant->user_id !== $user->id && ! $user->isAdmin()) {
                Log::warning('Food item update: unauthorized restaurant_id', [
                    'food_item_id' => $id,
                    'user_id' => $user->id,
                    'restaurant_id' => $data['restaurant_id'],
                ]);
                throw new AuthorizationException('Unauthorized');
            }
            $categoryId = (int) ($data['food_category_id'] ?? $foodItem->food_category_id);
            $regeneratedFoodCode = $this->generateFoodCode(
                $targetRestaurant->country->code,
                $targetRestaurant->code,
                $categoryId
            );
        }

        if (isset($data['price'], $data['currency_code'])) {
            $foodItem->price_usd = $this->convertToUSD($data['price'], $data['currency_code']);
        }

        $data = $this->sanitizeDescription($data);

        $payload = array_diff_key($data, array_flip(['food_code', 'food_code_status']));

        if ($regeneratedFoodCode !== null) {
            $payload['food_code'] = $regeneratedFoodCode;
            $payload['food_code_status'] = 'confirmed';
        }

        $foodItem->update($payload);

        Log::info('Food item updated', ['food_item_id' => $id, 'user_id' => $user->id]);

        return $foodItem->load(['restaurant', 'foodCategory']);
    }

    /**
     * Delete food item. Throws AuthorizationException if user is not owner or admin.
     *
     * @throws AuthorizationException
     */
    public function destroy(User $user, int $id): void
    {
        $foodItem = $this->foodItemRepository->findWithRelations($id);

        if ($foodItem->restaurant->user_id !== $user->id && ! $user->isAdmin()) {
            Log::warning('Food item delete unauthorized', ['food_item_id' => $id, 'user_id' => $user->id]);
            throw new AuthorizationException('Unauthorized');
        }

        $foodItem->delete();
        Log::info('Food item deleted', ['food_item_id' => $id, 'user_id' => $user->id]);
    }

    /**
     * Confirm food code and set status active (admin).
     */
    public function confirmFoodCode(int $id): FoodItem
    {
        $foodItem = $this->foodItemRepository->findOrFail($id);
        $foodItem->update(['food_code_status' => 'confirmed', 'status' => 'active']);

        Log::info('Food item code confirmed (admin)', ['food_item_id' => $id]);

        return $foodItem;
    }

    /**
     * Get food items with pending code confirmation (admin).
     */
    public function getPendingFoodCodes(): LengthAwarePaginator
    {
        return $this->foodItemRepository->getPendingCodeConfirmation(20);
    }

    /**
     * Admin: update food item status (active, hidden, pending).
     */
    public function updateStatus(int $id, string $status): FoodItem
    {
        $foodItem = $this->foodItemRepository->findOrFail($id);
        $foodItem->update(['status' => $status]);

        Log::info('Food item status updated (admin)', ['food_item_id' => $id, 'status' => $status]);

        return $foodItem;
    }

    /**
     * Admin: paginated food items for a restaurant with optional status filter.
     *
     * @param  array  $filters  status?
     */
    public function getRestaurantFoodItems(int $restaurantId, array $filters = []): LengthAwarePaginator
    {
        return $this->foodItemRepository->getByRestaurantId($restaurantId, $filters);
    }

    /**
     * Owner: get food items for a restaurant (includes hidden/disable).
     *
     * @param  array  $filters  status? , per_page?
     *
     * @throws AuthorizationException
     */
    public function getRestaurantFoodItemsForOwner(User $user, int $restaurantId, array $filters = []): LengthAwarePaginator
    {
        $restaurant = $this->restaurantRepository->findOrFail($restaurantId);

        if ($restaurant->user_id !== $user->id && ! $user->isAdmin()) {
            throw new AuthorizationException('Unauthorized');
        }

        return $this->foodItemRepository->getByRestaurantId($restaurantId, $filters);
    }

    /**
     * Map legacy JSON keys vn/kr to vi/ko so stored name uses en, vi, ko consistently.
     *
     * @param  array<string, string|null>  $name
     * @return array<string, string|null>
     */
    protected function normalizeFoodItemNameKeys(array $name): array
    {
        if (isset($name['vn']) && ! isset($name['vi'])) {
            $name['vi'] = $name['vn'];
        }
        if (isset($name['kr']) && ! isset($name['ko'])) {
            $name['ko'] = $name['kr'];
        }

        return $name;
    }

    /**
     * Sanitize description (CKEditor HTML) for WYSIWYG display.
     */
    protected function sanitizeDescription(array $data): array
    {
        if (isset($data['description']) && is_array($data['description'])) {
            $data['description'] = HtmlSanitizer::sanitizeArray($data['description']);
        }

        return $data;
    }

    /**
     * Generate unique food code: {countryCode}-{restaurantCode}-{categoryCode}-{seq}.
     */
    protected function generateFoodCode(string $countryCode, string $restaurantCode, int $categoryId): string
    {
        $category = $this->foodCategoryRepository->findOrFail($categoryId);
        $categoryCode = str_pad($category->code, 4, '0', STR_PAD_LEFT);

        $codePrefix = "{$countryCode}-{$restaurantCode}-{$categoryCode}-";
        $lastFoodItem = $this->foodItemRepository->getLastByCodePrefix($codePrefix);

        $newNumber = $lastFoodItem ? (int) substr($lastFoodItem->food_code, -4) + 1 : 1;

        return sprintf('%s-%s-%s-%04d', $countryCode, $restaurantCode, $categoryCode, $newNumber);
    }

    /**
     * Convert amount to USD using ExchangeRate repository.
     */
    protected function convertToUSD(float $amount, string $currencyCode): float
    {
        if ($currencyCode === 'USD') {
            return $amount;
        }

        $date = now()->toDateString();
        $exchangeRate = $this->exchangeRateRepository->findRate($currencyCode, 'USD', $date);

        return $exchangeRate ? (float) ($amount * $exchangeRate->rate) : $amount;
    }
}
