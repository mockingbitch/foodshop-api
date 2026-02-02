<?php

namespace App\Repositories;

use App\Contracts\Repositories\FoodCategoryRepositoryInterface;
use App\Models\FoodCategory;
use App\Models\FoodCategoryTranslation;
use Illuminate\Database\Eloquent\Collection;

/**
 * Food category repository: Eloquent query layer for FoodCategory and translations.
 * Handles list (active, root/parent filter), find with relations, create, update, delete, translations.
 */
class FoodCategoryRepository extends BaseRepository implements FoodCategoryRepositoryInterface
{
    public function __construct(FoodCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active categories with optional root_only and parent_id filter, ordered by sort_order.
     *
     * @param array $filters root_only?, parent_id?
     * @return Collection
     */
    public function getActiveList(array $filters): Collection
    {
        $query = $this->query()
            ->with(['translations', 'parent', 'children'])
            ->active();

        if (!empty($filters['root_only'])) {
            $query->rootCategories();
        }
        if (array_key_exists('parent_id', $filters) && $filters['parent_id'] !== null && $filters['parent_id'] !== '') {
            $query->where('parent_id', $filters['parent_id']);
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * Find category by ID with relations (translations, parent, children).
     *
     * @param int $id
     * @return FoodCategory
     */
    public function findWithRelations(int $id): FoodCategory
    {
        return $this->query()
            ->with(['translations', 'parent', 'children'])
            ->findOrFail($id);
    }

    /**
     * Create category and attach translations.
     *
     * @param array $data Without 'translations' key for main model
     * @param array $translations Array of translation rows
     * @return FoodCategory
     */
    public function createWithTranslations(array $data, array $translations): FoodCategory
    {
        unset($data['translations']);
        $category = $this->model->create($data);

        foreach ($translations as $t) {
            $category->translations()->create($t);
        }

        return $category->load('translations');
    }

    /**
     * Update category (excluding translations key).
     *
     * @param int $id
     * @param array $data
     * @return FoodCategory
     */
    public function updateCategory(int $id, array $data): FoodCategory
    {
        $category = $this->findOrFail($id);
        unset($data['translations']);
        $category->update($data);

        return $category->load('translations');
    }

    /**
     * Update or create translation for category.
     *
     * @param int $categoryId
     * @param array $data language_code, name, description?, video_link?
     * @return FoodCategoryTranslation
     */
    public function updateOrCreateTranslation(int $categoryId, array $data): FoodCategoryTranslation
    {
        return FoodCategoryTranslation::updateOrCreate(
            [
                'food_category_id' => $categoryId,
                'language_code' => $data['language_code'],
            ],
            array_intersect_key($data, array_flip(['name', 'description', 'video_link']))
        );
    }

    /**
     * Count children of category.
     *
     * @param int $id
     * @return int
     */
    public function countChildren(int $id): int
    {
        return $this->findOrFail($id)->children()->count();
    }

    /**
     * Count food items in category.
     *
     * @param int $id
     * @return int
     */
    public function countFoodItems(int $id): int
    {
        return $this->findOrFail($id)->foodItems()->count();
    }
}
