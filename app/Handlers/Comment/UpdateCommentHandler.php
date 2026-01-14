<?php

namespace App\Handlers\Comment;

use App\Data\Comment\CommentData;
use App\Data\Comment\UpdateCommentData;
use App\Models\Comment;

class UpdateCommentHandler
{
    public function handle(int $id, UpdateCommentData $dto): CommentData
    {
        $comment = Comment::with('user')->findOrFail($id);

        $comment->content = $dto->content;
        $comment->save();

        return CommentData::fromModel($comment);
    }
}
