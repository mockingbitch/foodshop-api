<?php

namespace App\Contracts\Repositories;

use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * News repository contract.
 */
interface NewsRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginated list of published news with optional type and search.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPublishedPaginated(array $filters): LengthAwarePaginator;

    /**
     * Get published news by type.
     *
     * @param string $type
     * @return LengthAwarePaginator
     */
    public function getPublishedByType(string $type): LengthAwarePaginator;

    /**
     * Find news by ID with category relation.
     *
     * @param int $id
     * @return News
     */
    public function findWithCategory(int $id): News;

    /**
     * Increment view_count for news by ID.
     *
     * @param int $id
     * @return void
     */
    public function incrementViewCount(int $id): void;

    public function count(): int;

    public function countPublished(): int;

    public function countDraft(): int;
}
