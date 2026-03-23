<?php

namespace App\Repositories;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedPaginated(array $filters): LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query()->with(['category'])->published();

        if (!empty($filters['type'])) {
            $query->type($filters['type']);
        }
        if (! empty($filters['search'])) {
            $pattern = '%' . mb_strtolower(trim($filters['search']), 'UTF-8') . '%';
            $query->where(function ($q) use ($pattern) {
                $driver = DB::getDriverName();
                if ($driver === 'pgsql') {
                    $q->whereRaw('LOWER(COALESCE(title->>\'en\', \'\')) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(COALESCE(title->>\'vn\', \'\')) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(COALESCE(title->>\'kr\', \'\')) LIKE ?', [$pattern]);
                } else {
                    $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.en"))) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.vn"))) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.kr"))) LIKE ?', [$pattern]);
                }
            });
        }

        $query->orderBy('published_at', 'desc');
        $query->orderBy('id', 'desc');

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * List for admin (all statuses). Optional filters: type, search, status, per_page. Paginated unless per_page=all.
     *
     * @param array $filters type?, search?, status?, per_page? (int or 'all')
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getPaginatedForAdmin(array $filters): LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query()->with(['category']);

        if (! empty($filters['type'])) {
            $query->type($filters['type']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $pattern = '%' . mb_strtolower(trim($filters['search']), 'UTF-8') . '%';
            $query->where(function ($q) use ($pattern) {
                $driver = DB::getDriverName();
                if ($driver === 'pgsql') {
                    $q->whereRaw('LOWER(COALESCE(title->>\'en\', \'\')) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(COALESCE(title->>\'vn\', \'\')) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(COALESCE(title->>\'kr\', \'\')) LIKE ?', [$pattern]);
                } else {
                    $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.en"))) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.vn"))) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.kr"))) LIKE ?', [$pattern]);
                }
            });
        }

        $query->orderByDesc('updated_at');
        $query->orderByDesc('id');

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $perPage = min(max($perPage, 1), 100);
        return $query->paginate($perPage);
    }

    /**
     * Get published news by type (news, course, chef). Paginated unless per_page=all.
     *
     * @param string $type
     * @param array $filters per_page? (int or 'all')
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedByType(string $type, array $filters = []): LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query()
            ->with(['category'])
            ->published()
            ->type($type)
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc');

        if (isset($filters['per_page']) && (string) $filters['per_page'] === 'all') {
            return $query->get();
        }
        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = min(max($perPage, 1), 100);

        return $query->paginate($perPage);
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
