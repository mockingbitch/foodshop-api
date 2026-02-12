<?php

namespace App\Repositories;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * News repository: Eloquent query layer for News (news/course/chef).
 * Handles list published (type, search), getByType, find with category, create, update, delete.
 */
class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    public function __construct(News $model)
    {
        parent::__construct($model);
    }

    /**
     * List of published news with optional type and search. Paginated unless per_page=all.
     *
     * @param array $filters type?, search?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getPublishedPaginated(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()->with(['category'])->published();

        if (!empty($filters['type'])) {
            $query->type($filters['type']);
        }
        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', $search)
                    ->orWhere('title->vn', 'like', $search)
                    ->orWhere('title->kr', 'like', $search);
            });
        }

        $query->orderBy('published_at', 'desc');

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * List for admin (all statuses). Optional filters: type, search, status, per_page. Paginated unless per_page=all.
     *
     * @param array $filters type?, search?, status?, per_page? (int or 'all')
     * @return LengthAwarePaginator|EloquentCollection
     */
    public function getPaginatedForAdmin(array $filters): LengthAwarePaginator|EloquentCollection
    {
        $query = $this->query()->with(['category']);

        if (! empty($filters['type'])) {
            $query->type($filters['type']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', $search)
                    ->orWhere('title->vn', 'like', $search)
                    ->orWhere('title->kr', 'like', $search);
            });
        }

        $query->orderByDesc('updated_at');

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $perPage = min(max($perPage, 1), 100);
        return $query->paginate($perPage);
    }

    /**
     * Get published news by type (news, course, chef).
     *
     * @param string $type
     * @return LengthAwarePaginator
     */
    public function getPublishedByType(string $type): LengthAwarePaginator
    {
        return $this->query()
            ->with(['category'])
            ->published()
            ->type($type)
            ->orderBy('published_at', 'desc')
            ->paginate(15);
    }

    /**
     * Find news by ID with category relation.
     *
     * @param int $id
     * @return News
     */
    public function findWithCategory(int $id): News
    {
        return $this->query()->with(['category'])->findOrFail($id);
    }

    /**
     * Increment view_count for news by ID.
     *
     * @param int $id
     * @return void
     */
    public function incrementViewCount(int $id): void
    {
        $this->findOrFail($id)->increment('view_count');
    }

    /** Count all news. */
    public function count(): int
    {
        return $this->query()->count();
    }

    /** Count published news. */
    public function countPublished(): int
    {
        return $this->query()->published()->count();
    }

    /** Count draft news. */
    public function countDraft(): int
    {
        return $this->query()->draft()->count();
    }
}
