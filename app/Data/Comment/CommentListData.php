<?php

namespace App\Data\Comment;

use Spatie\LaravelData\Data;

class CommentListData extends Data
{
    /**
     * @param CommentData[] $data
     */
    public function __construct(
        public array $data,
        public int $limit,
        public ?int $next_cursor = null,
        public bool $has_more = false,
    ) {
    }
}
