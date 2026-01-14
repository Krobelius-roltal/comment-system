<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VideoPost\CreateVideoPostHandler;
use App\Handlers\VideoPost\ShowVideoPostHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVideoPostRequest;
use App\Http\Requests\ShowVideoPostRequest;
use Illuminate\Http\JsonResponse;

class VideoPostController extends Controller
{
    public function __construct(
        private readonly CreateVideoPostHandler $create_video_post_handler,
        private readonly ShowVideoPostHandler $show_video_post_handler,
    ) {
    }

    public function store(CreateVideoPostRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $video_post_data = $this->create_video_post_handler->handle($dto);

        return response()->json($video_post_data->toArray(), 201);
    }

    public function show(int $id, ShowVideoPostRequest $request): JsonResponse
    {
        $pagination_dto = $request->toDto();
        $data = $this->show_video_post_handler->handle($id, $pagination_dto);

        return response()->json($data);
    }
}
