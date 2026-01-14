<?php

namespace App\Handlers\Comment;

use App\Data\Comment\CommentData;
use App\Data\Comment\UpdateCommentData;
use App\Exceptions\CommentNotFoundException;
use App\Models\Comment;

class UpdateCommentHandler
{
    public function handle(int $id, UpdateCommentData $dto): CommentData
    {
        $comment = Comment::with('user')->find($id);
        if (!$comment) {
            throw new CommentNotFoundException($id);
        }

        $comment->content = $dto->content;
        $comment->save();

        return CommentData::fromModel($comment);
    }
}
