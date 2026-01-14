<?php

namespace App\Handlers\Comment;

use App\Data\Comment\CommentData;
use App\Data\Comment\CommentIndexData;
use App\Models\Comment;
use App\Services\CommentableTypeService;

readonly class IndexCommentHandler
{
    public function __construct(
        private CommentableTypeService $commentable_type_service,
    ) {
    }

    public function handle(CommentIndexData $dto): array
    {
        $model_class = $this->commentable_type_service->getModelClass($dto->commentable_type);

        $this->commentable_type_service->validateCommentableExists($dto->commentable_type, $dto->commentable_id);

        $query = Comment::with('user')
            ->where('commentable_type', $model_class)
            ->where('commentable_id', $dto->commentable_id);

        if ($dto->parent_id !== null) {
            $query->where('parent_id', $dto->parent_id);
        }

        $limit = $dto->getLimit();
        $offset = $dto->getOffset();
        $total = $query->count();

        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $comments_data = $comments->map(fn($comment) => CommentData::fromModel($comment)->toArray());

        return [
            'data' => $comments_data->toArray(),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total,
        ];
    }
}
