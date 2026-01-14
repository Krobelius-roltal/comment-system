<?php

namespace App\Data\VideoPost;

use App\Models\VideoPost;
use Spatie\LaravelData\Data;

class VideoPostData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $created_at,
        public string $updated_at,
    ) {
    }

    public static function fromModel(VideoPost $videoPost): self
    {
        return new self(
            id: $videoPost->id,
            title: $videoPost->title,
            description: $videoPost->description,
            created_at: $videoPost->created_at->toIso8601String(),
            updated_at: $videoPost->updated_at->toIso8601String(),
        );
    }
}
