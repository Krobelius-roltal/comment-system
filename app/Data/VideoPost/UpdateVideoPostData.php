<?php

namespace App\Data\VideoPost;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateVideoPostData extends Data
{
    public function __construct(
        #[StringType, Max(255)]
        public ?string $title = null,
        #[StringType]
        public ?string $description = null,
    ) {
    }
}
