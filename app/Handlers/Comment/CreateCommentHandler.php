<?php

namespace App\Handlers\Comment;

use App\Data\Comment\CommentData;
use App\Data\Comment\CreateCommentData;
use App\Exceptions\CommentNotFoundException;
use App\Models\Comment;
use App\Services\CommentableTypeService;

readonly class CreateCommentHandler
{
    public function __construct(
        private CommentableTypeService $commentable_type_service,
    ) {
    }

    public function handle(CreateCommentData $dto): CommentData
    {
        $model_class = $this->commentable_type_service->getModelClass($dto->commentable_type);

        $this->commentable_type_service->validateCommentableExists($dto->commentable_type, $dto->commentable_id);

        if ($dto->parent_id !== null) {
            $parent_comment = Comment::find($dto->parent_id);
            if (!$parent_comment) {
                throw new CommentNotFoundException($dto->parent_id);
            }
        }

        $comment = Comment::create([
            'user_id' => $dto->user_id,
            'commentable_type' => $model_class,
            'commentable_id' => $dto->commentable_id,
            'parent_id' => $dto->parent_id,
            'content' => $dto->content,
        ]);

        $comment->load('user');

        return CommentData::fromModel($comment);
    }
}
