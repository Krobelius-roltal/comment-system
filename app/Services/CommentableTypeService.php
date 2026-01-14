<?php

namespace App\Services;

use App\Exceptions\NewsNotFoundException;
use App\Exceptions\VideoPostNotFoundException;
use App\Models\News;
use App\Models\VideoPost;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class CommentableTypeService
{
    public function getModelClass(string $type): string
    {
        return match ($type) {
            'news' => News::class,
            'video-post' => VideoPost::class,
            default => throw new InvalidArgumentException("Invalid commentable_type: {$type}"),
        };
    }

    public function validateCommentableExists(string $type, int $id): void
    {
        $model_class = $this->getModelClass($type);
        /** @var Model $model_class */
        $model = $model_class::find($id);
        
        if (!$model) {
            match ($type) {
                'news' => throw new NewsNotFoundException($id),
                'video-post' => throw new VideoPostNotFoundException($id),
                default => throw new InvalidArgumentException("Invalid commentable_type: {$type}"),
            };
        }
    }
}
