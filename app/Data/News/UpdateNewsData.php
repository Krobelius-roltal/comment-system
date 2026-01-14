<?php

namespace App\Data\News;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateNewsData extends Data
{
    public function __construct(
        #[StringType, Max(255)]
        public ?string $title = null,
        #[StringType]
        public ?string $description = null,
    ) {
    }
}
