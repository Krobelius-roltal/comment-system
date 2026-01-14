<?php

namespace App\Data\News;

use App\Models\News;
use Spatie\LaravelData\Data;

class NewsData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $created_at,
        public string $updated_at,
    ) {
    }

    public static function fromModel(News $news): self
    {
        return new self(
            id: $news->id,
            title: $news->title,
            description: $news->description,
            created_at: $news->created_at->toIso8601String(),
            updated_at: $news->updated_at->toIso8601String(),
        );
    }
}
