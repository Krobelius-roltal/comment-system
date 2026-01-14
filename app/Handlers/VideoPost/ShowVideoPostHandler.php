<?php

namespace App\Handlers\VideoPost;

use App\Data\Comment\CommentData;
use App\Data\Common\PaginationData;
use App\Data\VideoPost\VideoPostData;
use App\Models\VideoPost;

class ShowVideoPostHandler
{
    public function handle(int $id, PaginationData $pagination_dto): array
    {
        $video_post = VideoPost::findOrFail($id);

        $limit = $pagination_dto->getLimit();
        $offset = $pagination_dto->getOffset();

        $comments_query = $video_post->comments()->with('user');
        $total = $comments_query->count();

        $comments = $comments_query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $video_post_data = VideoPostData::fromModel($video_post);
        $comments_data = $comments->map(fn($comment) => CommentData::fromModel($comment));

        return [
            ...$video_post_data->toArray(),
            'comments' => [
                'data' => $comments_data->map(fn($data) => $data->toArray())->toArray(),
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total,
            ],
        ];
    }
}
