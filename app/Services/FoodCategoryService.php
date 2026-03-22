<?php

namespace App\Services;

use App\Contracts\Repositories\FoodCategoryRepositoryInterface;
use App\Models\FoodCategory;
use App\Models\FoodCategoryTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Food category business logic: list, show, store, update, destroy, add translation.
 * Admin-only write operations; delete fails if category has children or food items.
 */
class FoodCategoryService
{
    public function __construct(
        protected FoodCategoryRepositoryInterface $foodCategoryRepository
    ) {}

    /**
     * List active categories with optional filters. Paginated unless per_page=all.
     *
     * @param array $filters root_only?, parent_id?, per_page? (int or 'all')
     * @return LengthAwarePaginator|Collection
     */
    public function index(array $filters): LengthAwarePaginator|Collection
    {
        return $this->foodCategoryRepository->getActiveList($filters);
    }

    /**
     * Get category by ID with translation for language code and images.
     *
     * @param int $id
     * @param string $languageCode
     * @return array{category: FoodCategory, translation: FoodCategoryTranslation|null, images: array}
     */
    public function show(int $id, string $languageCode = 'en'): array
    {
        $category = $this->foodCategoryRepository->findWithRelations($id);
        $translation = $category->getTranslation($languageCode);

        return [
            'category' => $category,
            'translation' => $translation,
            'images' => $category->getImages(),
        ];
    }

    /**
     * Create category with translations.
     *
     * @param array $data Validated (code, parent_id, image_1-5, sort_order, translations)
     * @return FoodCategory
     */
    public function store(array $data): FoodCategory
    {
        $translations = $data['translations'] ?? [];
        $category = $this->foodCategoryRepository->createWithTranslations($data, $translations);
        Log::info('Food category created', ['category_id' => $category->id, 'code' => $category->code]);
        return $category;
    }

    /**
     * Update category and optionally translations.
     *
     * @param int $id
     * @param array $data code?, parent_id?, image_1-5?, sort_order?, translations? (array of { language_code, name, description?, video_link? })
     * @return FoodCategory
     */
    public function update(int $id, array $data): FoodCategory
    {
        $translations = $data['translations'] ?? null;
        unset($data['translations']);

        $category = $this->foodCategoryRepository->updateCategory($id, $data);

        if (! empty($translations)) {
            foreach ($translations as $t) {
                $this->foodCategoryRepository->updateOrCreateTranslation($id, $t);
            }
            $category->load('translations');
        }

        Log::info('Food category updated', ['category_id' => $id]);
        return $category;
    }

    /**
     * Delete category. Throws ValidationException if has children or food items.
     *
     * @param int $id
     * @throws ValidationException
     */
    public function destroy(int $id): void
    {
        if ($this->foodCategoryRepository->countChildren($id) > 0) {
            Log::warning('Food category delete blocked: has subcategories', ['category_id' => $id]);
            throw ValidationException::withMessages(['category' => ['Cannot delete category with subcategories.']]);
        }

        if ($this->foodCategoryRepository->countFoodItems($id) > 0) {
            Log::warning('Food category delete blocked: has food items', ['category_id' => $id]);
            throw ValidationException::withMessages(['category' => ['Cannot delete category with food items.']]);
        }

        $this->foodCategoryRepository->delete($id);
        Log::info('Food category deleted', ['category_id' => $id]);
    }

    /**
     * Add or update translation for category.
     *
     * @param int $id Category ID
     * @param array $data language_code, name, description?, video_link?
     * @return FoodCategoryTranslation
     */
    public function addTranslation(int $id, array $data): FoodCategoryTranslation
    {
        $translation = $this->foodCategoryRepository->updateOrCreateTranslation($id, $data);
        Log::info('Food category translation added', ['category_id' => $id, 'language_code' => $data['language_code'] ?? null]);
        return $translation;
    }
}
