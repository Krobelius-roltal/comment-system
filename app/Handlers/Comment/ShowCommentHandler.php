<?php

namespace App\Handlers\Comment;

use App\Data\Comment\CommentData;
use App\Models\Comment;

class ShowCommentHandler
{
    public function handle(int $id): array
    {
        $comment = Comment::with(['user', 'commentable'])->findOrFail($id);

        $comment_data = CommentData::fromModel($comment);

        $data = $comment_data->toArray();
        $data['commentable'] = [
            'id' => $comment->commentable->id,
            'title' => $comment->commentable->title,
        ];

        return $data;
    }
}
