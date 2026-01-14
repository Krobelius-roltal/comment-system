<?php

namespace App\Handlers\VideoPost;

use App\Data\VideoPost\CreateVideoPostData;
use App\Data\VideoPost\VideoPostData;
use App\Models\VideoPost;

class CreateVideoPostHandler
{
    public function handle(CreateVideoPostData $dto): VideoPostData
    {
        $video_post = VideoPost::create([
            'title' => $dto->title,
            'description' => $dto->description,
        ]);

        return VideoPostData::fromModel($video_post);
    }
}
