<?php

namespace App\Contracts\Repositories;

use App\Models\FoodCategory;
use App\Models\FoodCategoryTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Food category repository contract.
 */
interface FoodCategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active categories with optional filters. Paginated unless per_page=all.
     *
     * @param array $filters root_only?, parent_id?, per_page? (int or 'all')
     * @return LengthAwarePaginator|Collection
     */
    public function getActiveList(array $filters): LengthAwarePaginator|Collection;

    /**
     * Find category by ID with relations.
     *
     * @param int $id
     * @return FoodCategory
     */
    public function findWithRelations(int $id): FoodCategory;

    /**
     * Create category and attach translations.
     *
     * @param array $data
     * @param array $translations
     * @return FoodCategory
     */
    public function createWithTranslations(array $data, array $translations): FoodCategory;

    /**
     * Update category (excluding translations key).
     *
     * @param int $id
     * @param array $data
     * @return FoodCategory
     */
    public function updateCategory(int $id, array $data): FoodCategory;

    /**
     * Update or create translation for category.
     *
     * @param int $categoryId
     * @param array $data
     * @return FoodCategoryTranslation
     */
    public function updateOrCreateTranslation(int $categoryId, array $data): FoodCategoryTranslation;

    /**
     * Count children of category.
     *
     * @param int $id
     * @return int
     */
    public function countChildren(int $id): int;

    /**
     * Count food items in category.
     *
     * @param int $id
     * @return int
     */
    public function countFoodItems(int $id): int;
}
