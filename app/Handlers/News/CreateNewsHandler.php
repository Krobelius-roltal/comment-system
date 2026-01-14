<?php

namespace App\Handlers\News;

use App\Data\News\CreateNewsData;
use App\Data\News\NewsData;
use App\Models\News;

class CreateNewsHandler
{
    public function handle(CreateNewsData $dto): NewsData
    {
        $news = News::create([
            'title' => $dto->title,
            'description' => $dto->description,
        ]);

        return NewsData::fromModel($news);
    }
}
