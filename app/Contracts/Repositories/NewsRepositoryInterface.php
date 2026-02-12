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
     * List of published news with optional type and search. Paginated unless per_page=all.
     *
     * @param array $filters
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedPaginated(array $filters): LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;

    /**
     * List for admin (all statuses). Filters: type?, search?, status?, per_page?. Paginated unless per_page=all.
     *
     * @param array $filters
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedForAdmin(array $filters): LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;

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
