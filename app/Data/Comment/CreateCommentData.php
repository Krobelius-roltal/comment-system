<?php

namespace App\Data\Comment;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateCommentData extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, In(['news', 'video-post'])]
        public string $commentable_type,

        #[Required, IntegerType]
        public int $commentable_id,

        #[IntegerType]
        public ?int $parent_id = null,

        #[Required, StringType]
        public string $content,
    ) {
    }
}
