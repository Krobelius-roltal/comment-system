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
        $cursor = $dto->getCursor();

        if ($cursor !== null) {
            $cursor_comment = Comment::find($cursor);
            if ($cursor_comment) {
                $query->where(function ($q) use ($cursor_comment, $cursor) {
                    $q->where('created_at', '<', $cursor_comment->created_at)
                        ->orWhere(function ($q2) use ($cursor_comment, $cursor) {
                            $q2->where('created_at', '=', $cursor_comment->created_at)
                                ->where('id', '<', $cursor);
                        });
                });
            }
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments->count() > $limit;
        if ($has_more) {
            $comments = $comments->take($limit);
        }

        $comments_data = $comments->map(fn($comment) => CommentData::fromModel($comment)->toArray());

        $next_cursor = $has_more ? $comments->last()->id : null;

        return [
            'data' => $comments_data->toArray(),
            'limit' => $limit,
            'next_cursor' => $next_cursor,
            'has_more' => $has_more,
        ];
    }
}
