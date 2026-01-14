<?php

namespace App\Http\Controllers\Api;

use App\Data\Comment\CommentData;
use App\Data\Comment\CreateCommentData;
use App\Data\Comment\UpdateCommentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\News;
use App\Models\VideoPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'commentable_type' => 'required|string|in:news,video-post',
            'commentable_id' => 'required|integer',
            'parent_id' => 'nullable|integer',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
        ]);

        $commentableType = $request->get('commentable_type');
        $commentableId = $request->get('commentable_id');
        $parentId = $request->get('parent_id');
        $limit = min((int) $request->get('limit', 10), 100);
        $offset = max((int) $request->get('offset', 0), 0);

        // Преобразуем тип в полное имя класса модели
        $modelClass = $this->getModelClass($commentableType);

        // Проверяем существование объекта
        $modelClass::findOrFail($commentableId);

        $query = Comment::with('user')
            ->where('commentable_type', $modelClass)
            ->where('commentable_id', $commentableId);

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        }

        $total = $query->count();

        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $commentsData = $comments->map(fn($comment) => CommentData::fromModel($comment)->toArray());

        return response()->json([
            'data' => $commentsData->toArray(),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total,
        ]);
    }

    public function store(CreateCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        // Преобразуем тип в полное имя класса модели
        $modelClass = $this->getModelClass($dto->commentable_type);

        // Проверяем существование объекта
        $modelClass::findOrFail($dto->commentable_id);

        // Если указан parent_id, проверяем существование родительского комментария
        if ($dto->parent_id !== null) {
            Comment::findOrFail($dto->parent_id);
        }

        $comment = Comment::create([
            'user_id' => $dto->user_id,
            'commentable_type' => $modelClass,
            'commentable_id' => $dto->commentable_id,
            'parent_id' => $dto->parent_id,
            'content' => $dto->content,
        ]);

        $comment->load('user');

        return response()->json(
            CommentData::fromModel($comment)->toArray(),
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $comment = Comment::with(['user', 'commentable'])->findOrFail($id);

        $commentData = CommentData::fromModel($comment);

        // Добавляем информацию о commentable
        $data = $commentData->toArray();
        $data['commentable'] = [
            'id' => $comment->commentable->id,
            'title' => $comment->commentable->title,
        ];

        return response()->json($data);
    }

    public function update(int $id, UpdateCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $comment = Comment::with('user')->findOrFail($id);

        $comment->content = $dto->content;
        $comment->save();

        return response()->json(
            CommentData::fromModel($comment)->toArray()
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(null, 204);
    }

    private function getModelClass(string $type): string
    {
        return match ($type) {
            'news' => News::class,
            'video-post' => VideoPost::class,
            default => throw new \InvalidArgumentException("Invalid commentable_type: {$type}"),
        };
    }
}
