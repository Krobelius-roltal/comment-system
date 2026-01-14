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
        #[IntegerType, Min(0)]
        public ?int $offset = 0,
    ) {
    }

    public function getLimit(): int
    {
        return min($this->limit ?? 10, 100);
    }

    public function getOffset(): int
    {
        return max($this->offset ?? 0, 0);
    }
}
