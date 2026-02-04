<?php

namespace App\Services;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * News/Course/Chef article business logic: list, getByType, show (with view increment), store, update, destroy.
 */
class NewsService
{
    public function __construct(
        protected NewsRepositoryInterface $newsRepository
    ) {}

    /**
     * Paginated list of published news with optional type and search.
     *
     * @param array $filters type?, search?, per_page?
     * @return LengthAwarePaginator
     */
    public function index(array $filters): LengthAwarePaginator
    {
        return $this->newsRepository->getPublishedPaginated($filters);
    }

    /**
     * Get published news by type (news, course, chef).
     *
     * @param string $type
     * @return LengthAwarePaginator
     */
    public function getByType(string $type): LengthAwarePaginator
    {
        return $this->newsRepository->getPublishedByType($type);
    }

    /**
     * Get news by ID and increment view count.
     *
     * @param int $id
     * @return News
     */
    public function show(int $id): News
    {
        $news = $this->newsRepository->findWithCategory($id);
        $this->newsRepository->incrementViewCount($id);

        return $news;
    }

    /**
     * Create news/course/chef article.
     *
     * @param array $data Validated store data
     * @return News
     */
    public function store(array $data): News
    {
        $news = $this->newsRepository->create($data);
        Log::info('News created', ['news_id' => $news->id, 'type' => $news->type ?? null]);
        return $news;
    }

    /**
     * Update news article.
     *
     * @param int $id
     * @param array $data
     * @return News
     */
    public function update(int $id, array $data): News
    {
        $news = $this->newsRepository->findOrFail($id);
        $news->update($data);
        Log::info('News updated', ['news_id' => $id]);
        return $news;
    }

    /**
     * Delete news article.
     *
     * @param int $id
     */
    public function destroy(int $id): void
    {
        $this->newsRepository->delete($id);
        Log::info('News deleted', ['news_id' => $id]);
    }
}
