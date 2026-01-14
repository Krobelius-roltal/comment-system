<?php

namespace App\Handlers\Comment;

use App\Models\Comment;

class DeleteCommentHandler
{
    public function handle(int $id): void
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
    }
}
