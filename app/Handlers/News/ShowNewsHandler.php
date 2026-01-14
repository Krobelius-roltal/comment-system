<?php

namespace App\Handlers\News;

use App\Data\Comment\CommentData;
use App\Data\Common\PaginationData;
use App\Data\News\NewsData;
use App\Models\News;

class ShowNewsHandler
{
    public function handle(int $id, PaginationData $pagination_dto): array
    {
        $news = News::findOrFail($id);

        $limit = $pagination_dto->getLimit();
        $offset = $pagination_dto->getOffset();

        $comments_query = $news->comments()->with('user');
        $total = $comments_query->count();

        $comments = $comments_query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $news_data = NewsData::fromModel($news);
        $comments_data = $comments->map(fn($comment) => CommentData::fromModel($comment));

        return [
            ...$news_data->toArray(),
            'comments' => [
                'data' => $comments_data->map(static fn($data) => $data->toArray())->toArray(),
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total,
            ],
        ];
    }
}
