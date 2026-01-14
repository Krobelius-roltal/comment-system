<?php

namespace App\Handlers\Comment;

use App\Exceptions\CommentNotFoundException;
use App\Models\Comment;

class DeleteCommentHandler
{
    public function handle(int $id): void
    {
        $comment = Comment::find($id);
        if (!$comment) {
            throw new CommentNotFoundException($id);
        }
        $comment->delete();
    }
}
