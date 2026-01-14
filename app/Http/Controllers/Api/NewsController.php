<?php

namespace App\Http\Controllers\Api;

use App\Data\Comment\CommentData;
use App\Data\News\CreateNewsData;
use App\Data\News\NewsData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewsRequest;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function store(CreateNewsRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        $news = News::create([
            'title' => $dto->title,
            'description' => $dto->description,
        ]);

        return response()->json(
            NewsData::fromModel($news)->toArray(),
            201
        );
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $news = News::findOrFail($id);

        $limit = min((int) $request->get('limit', 10), 100);
        $offset = max((int) $request->get('offset', 0), 0);

        $commentsQuery = $news->comments()->with('user');
        $total = $commentsQuery->count();

        $comments = $commentsQuery->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $newsData = NewsData::fromModel($news);
        $commentsData = $comments->map(fn($comment) => CommentData::fromModel($comment));

        return response()->json([
            ...$newsData->toArray(),
            'comments' => [
                'data' => $commentsData->map(fn($data) => $data->toArray())->toArray(),
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total,
            ],
        ]);
    }
}
