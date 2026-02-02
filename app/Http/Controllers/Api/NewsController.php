<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    /**
     * Get list of published news (filters: type, search, per_page)
     */
    public function index(Request $request): JsonResponse
    {
        $news = $this->newsService->index($request->only(['type', 'search', 'per_page']));

        return response()->json($news);
    }

    /**
     * Get news by type (news, course, chef)
     */
    public function getByType(string $type): JsonResponse
    {
        $news = $this->newsService->getByType($type);

        return response()->json($news);
    }

    /**
     * Get news/article details by ID (increments view_count)
     */
    public function show(int $id): JsonResponse
    {
        $news = $this->newsService->show($id);

        return response()->json($news);
    }

    /**
     * Create news/course/chef article (Admin).
     */
    public function store(StoreNewsRequest $request): JsonResponse
    {
        $news = $this->newsService->store($request->validated());

        return response()->json([
            'message' => 'News created successfully',
            'news' => $news,
        ], 201);
    }

    /**
     * Update news/course/chef article (Admin).
     */
    public function update(UpdateNewsRequest $request, int $id): JsonResponse
    {
        $news = $this->newsService->update($id, $request->validated());

        return response()->json([
            'message' => 'News updated successfully',
            'news' => $news,
        ]);
    }

    /**
     * Delete news/course/chef article (Admin).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->newsService->destroy($id);

        return response()->json(['message' => 'News deleted successfully']);
    }
}
