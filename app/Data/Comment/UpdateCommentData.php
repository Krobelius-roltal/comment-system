<?php

namespace App\Data\Comment;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateCommentData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $content,
    ) {
    }
}
