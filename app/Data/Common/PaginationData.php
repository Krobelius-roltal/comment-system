<?php

namespace App\Data\Common;

use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class PaginationData extends Data
{
    public function __construct(
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
