<?php

namespace App\Handlers\News;

use App\Data\Comment\CommentData;
use App\Data\Common\PaginationData;
use App\Data\News\NewsData;
use App\Exceptions\NewsNotFoundException;
use App\Models\News;

class ShowNewsHandler
{
    public function handle(int $id, PaginationData $pagination_dto): array
    {
        $news = News::find($id);
        if (!$news) {
            throw new NewsNotFoundException($id);
        }

        $limit = $pagination_dto->getLimit();
        $cursor = $pagination_dto->getCursor();

        $comments_query = $news->comments()->with('user');

        if ($cursor !== null) {
            $cursor_comment = $news->comments()->find($cursor);
            if ($cursor_comment) {
                $comments_query->where(function ($query) use ($cursor_comment, $cursor) {
                    $query->where('created_at', '<', $cursor_comment->created_at)
                        ->orWhere(function ($q) use ($cursor_comment, $cursor) {
                            $q->where('created_at', '=', $cursor_comment->created_at)
                                ->where('id', '<', $cursor);
                        });
                });
            }
        }

        $comments = $comments_query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments->count() > $limit;
        if ($has_more) {
            $comments = $comments->take($limit);
        }

        $news_data = NewsData::fromModel($news);
        $comments_data = $comments->map(fn($comment) => CommentData::fromModel($comment));

        $next_cursor = $has_more ? $comments->last()->id : null;

        return [
            ...$news_data->toArray(),
            'comments' => [
                'data' => $comments_data->map(static fn($data) => $data->toArray())->toArray(),
                'limit' => $limit,
                'next_cursor' => $next_cursor,
                'has_more' => $has_more,
            ],
        ];
    }
}
