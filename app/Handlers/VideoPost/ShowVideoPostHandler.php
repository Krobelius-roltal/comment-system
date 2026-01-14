<?php

namespace App\Handlers\VideoPost;

use App\Data\Comment\CommentData;
use App\Data\Common\PaginationData;
use App\Data\VideoPost\VideoPostData;
use App\Exceptions\VideoPostNotFoundException;
use App\Models\VideoPost;

class ShowVideoPostHandler
{
    public function handle(int $id, PaginationData $pagination_dto): array
    {
        $video_post = VideoPost::find($id);
        if (!$video_post) {
            throw new VideoPostNotFoundException($id);
        }

        $limit = $pagination_dto->getLimit();
        $cursor = $pagination_dto->getCursor();

        $comments_query = $video_post->comments()->with('user');

        if ($cursor !== null) {
            $cursor_comment = $video_post->comments()->find($cursor);
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

        $video_post_data = VideoPostData::fromModel($video_post);
        $comments_data = $comments->map(fn($comment) => CommentData::fromModel($comment));

        $next_cursor = $has_more ? $comments->last()->id : null;

        return [
            ...$video_post_data->toArray(),
            'comments' => [
                'data' => $comments_data->map(fn($data) => $data->toArray())->toArray(),
                'limit' => $limit,
                'next_cursor' => $next_cursor,
                'has_more' => $has_more,
            ],
        ];
    }
}
