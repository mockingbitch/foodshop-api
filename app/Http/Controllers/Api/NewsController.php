<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * News/Course/Chef CRUD (Admin). Public list/show by type; create/update/delete protected.
 *
 * @group News
 */
class NewsController extends BaseApiController
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    /**
     * Get list of published news (filters: type, search, per_page; per_page=all to disable pagination). Public.
     */
    public function index(Request $request): JsonResponse
    {
        $news = $this->newsService->index($request->only(['type', 'search', 'per_page']));

        return $this->successList($news);
    }

    /**
     * Get list of all news for admin (draft + published + archived). Filters: type, search, status, per_page (or per_page=all).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $news = $this->newsService->adminIndex($request->only(['type', 'search', 'status', 'per_page']));

        return $this->successList($news);
    }

    /**
     * Get news by type (news, course, chef)
     */
    public function getByType(string $type): JsonResponse
    {
        $news = $this->newsService->getByType($type);

        return $this->success($news);
    }

    /**
     * Get news/article details by ID (increments view_count)
     */
    public function show(int $id): JsonResponse
    {
        $news = $this->newsService->show($id);

        return $this->success($news);
    }

    /**
     * Create news/course/chef article (Admin).
     */
    public function store(StoreNewsRequest $request): JsonResponse
    {
        $news = $this->newsService->store($request->validated());

        return $this->created(['news' => $news], 'News created successfully');
    }

    /**
     * Update news/course/chef article (Admin).
     */
    public function update(UpdateNewsRequest $request, int $id): JsonResponse
    {
        $news = $this->newsService->update($id, $request->validated());

        return $this->success(['news' => $news], 'News updated successfully');
    }

    /**
     * Delete news/course/chef article (Admin).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->newsService->destroy($id);

        return $this->success(null, 'News deleted successfully');
    }
}
