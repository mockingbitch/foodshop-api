<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

/**
 * @group Endpoints
 */
class NewsController extends Controller
{
    /**
     * GET api/news
     * 
     * Get list of published news/articles
     * 
     * @queryParam type string Filter by type (news, course, chef). Example: news
     * @queryParam search string Search by title. Example: pizza
     * @queryParam per_page integer Items per page. Example: 15
     */
    public function index(Request $request)
    {
        $query = News::with(['category'])->published();

        // Filter by type (news, course, chef)
        if ($request->has('type')) {
            $query->type($request->type);
        }

        // Search by title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(title, '$.en') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_EXTRACT(title, '$.vn') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_EXTRACT(title, '$.kr') LIKE ?", ["%{$search}%"]);
            });
        }

        $news = $query->orderBy('published_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($news);
    }

    /**
     * GET api/news/by-type/{type}
     * 
     * Get news/articles by type
     * 
     * @urlParam type string required The type of news (news, course, chef). Example: consequatur
     */
    public function getByType($type)
    {
        $news = News::with(['category'])
            ->published()
            ->type($type)
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return response()->json($news);
    }

    /**
     * GET api/news/{id}
     * 
     * Get news/article details by ID
     * 
     * @urlParam id integer required The ID of the news. Example: 17
     */
    public function show($id)
    {
        $news = News::with(['category'])->findOrFail($id);

        // Increment view count
        $news->increment('view_count');

        return response()->json($news);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:news,course,chef',
            'category_id' => 'nullable|exists:food_categories,id',
            'title' => 'required|array',
            'title.en' => 'required|string',
            'content' => 'required|array',
            'content.en' => 'required|string',
            'excerpt' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'video_link' => 'nullable|string',
            'chef_name' => 'nullable|string',
            'chef_specialty' => 'nullable|string',
            'course_price' => 'nullable|numeric',
            'course_duration' => 'nullable|integer',
            'max_participants' => 'nullable|integer',
            'status' => 'required|in:published,draft,archived',
            'published_at' => 'nullable|date',
        ]);

        $news = News::create($request->all());

        return response()->json([
            'message' => 'News created successfully',
            'news' => $news,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'type' => 'sometimes|in:news,course,chef',
            'category_id' => 'nullable|exists:food_categories,id',
            'title' => 'sometimes|array',
            'content' => 'sometimes|array',
            'excerpt' => 'nullable|array',
            'featured_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'video_link' => 'nullable|string',
            'status' => 'sometimes|in:published,draft,archived',
            'published_at' => 'nullable|date',
        ]);

        $news->update($request->all());

        return response()->json([
            'message' => 'News updated successfully',
            'news' => $news,
        ]);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json([
            'message' => 'News deleted successfully',
        ]);
    }
}
