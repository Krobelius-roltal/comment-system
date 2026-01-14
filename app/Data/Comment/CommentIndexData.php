<?php

namespace App\Data\Comment;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CommentIndexData extends Data
{
    public function __construct(
        #[Required, StringType, In(['news', 'video-post'])]
        public string $commentable_type,
        #[Required, IntegerType]
        public int $commentable_id,
        #[IntegerType]
        public ?int $parent_id = null,
        #[IntegerType, Min(1), Max(100)]
        public ?int $limit = 10,
        #[IntegerType]
        public ?int $cursor = null,
    ) {
    }

    public function getLimit(): int
    {
        return min($this->limit ?? 10, 100);
    }

    public function getCursor(): ?int
    {
        return $this->cursor;
    }
}
