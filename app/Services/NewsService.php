<?php

namespace App\Services;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Models\News;
use App\Support\HtmlSanitizer;
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
     * Paginated list of published news with optional type and search (public).
     *
     * @param array $filters type?, search?, per_page?
     * @return LengthAwarePaginator
     */
    public function index(array $filters): LengthAwarePaginator
    {
        return $this->newsRepository->getPublishedPaginated($filters);
    }

    /**
     * Paginated list for admin (all statuses). Filters: type?, search?, status?, per_page?.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function adminIndex(array $filters): LengthAwarePaginator
    {
        return $this->newsRepository->getPaginatedForAdmin($filters);
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
     * Create news/course/chef article. Content and excerpt are sanitized for WYSIWYG (safe HTML).
     *
     * @param array $data Validated store data
     * @return News
     */
    public function store(array $data): News
    {
        $data = $this->sanitizeWysiwygFields($data);
        $news = $this->newsRepository->create($data);
        Log::info('News created', ['news_id' => $news->id, 'type' => $news->type ?? null]);
        return $news;
    }

    /**
     * Update news article. Content and excerpt are sanitized for WYSIWYG (safe HTML).
     *
     * @param int $id
     * @param array $data
     * @return News
     */
    public function update(int $id, array $data): News
    {
        $data = $this->sanitizeWysiwygFields($data);
        $news = $this->newsRepository->findOrFail($id);
        $news->update($data);
        Log::info('News updated', ['news_id' => $id]);
        return $news;
    }

    /**
     * Sanitize content and excerpt (multilingual HTML) for WYSIWYG display.
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeWysiwygFields(array $data): array
    {
        if (isset($data['content']) && is_array($data['content'])) {
            $data['content'] = HtmlSanitizer::sanitizeArray($data['content']);
        }
        if (isset($data['excerpt']) && is_array($data['excerpt'])) {
            $data['excerpt'] = HtmlSanitizer::sanitizeArray($data['excerpt']);
        }
        return $data;
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
