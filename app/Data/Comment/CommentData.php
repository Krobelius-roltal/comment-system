<?php

namespace App\Data\Comment;

use App\Data\User\UserData;
use App\Models\Comment;
use Spatie\LaravelData\Data;

class CommentData extends Data
{
    public function __construct(
        public int $id,
        public int $user_id,
        public string $commentable_type,
        public int $commentable_id,
        public ?int $parent_id,
        public string $content,
        public string $created_at,
        public string $updated_at,
        public ?UserData $user = null,
    ) {
    }

    public static function fromModel(Comment $comment): self
    {
        return new self(
            id: $comment->id,
            user_id: $comment->user_id,
            commentable_type: $comment->commentable_type,
            commentable_id: $comment->commentable_id,
            parent_id: $comment->parent_id,
            content: $comment->content,
            created_at: $comment->created_at->toIso8601String(),
            updated_at: $comment->updated_at->toIso8601String(),
            user: $comment->relationLoaded('user') ? UserData::fromModel($comment->user) : null,
        );
    }
}
