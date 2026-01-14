<?php

namespace App\Http\Controllers\Api;

use App\Data\Comment\CommentData;
use App\Data\VideoPost\CreateVideoPostData;
use App\Data\VideoPost\VideoPostData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVideoPostRequest;
use App\Models\VideoPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function store(CreateVideoPostRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        $videoPost = VideoPost::create([
            'title' => $dto->title,
            'description' => $dto->description,
        ]);

        return response()->json(
            VideoPostData::fromModel($videoPost)->toArray(),
            201
        );
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $videoPost = VideoPost::findOrFail($id);

        $limit = min((int) $request->get('limit', 10), 100);
        $offset = max((int) $request->get('offset', 0), 0);

        $commentsQuery = $videoPost->comments()->with('user');
        $total = $commentsQuery->count();

        $comments = $commentsQuery->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $videoPostData = VideoPostData::fromModel($videoPost);
        $commentsData = $comments->map(fn($comment) => CommentData::fromModel($comment));

        return response()->json([
            ...$videoPostData->toArray(),
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
