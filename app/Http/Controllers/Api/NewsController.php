<?php

namespace App\Http\Controllers\Api;

use App\Handlers\News\CreateNewsHandler;
use App\Handlers\News\ShowNewsHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewsRequest;
use App\Http\Requests\ShowNewsRequest;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function __construct(
        private readonly CreateNewsHandler $create_news_handler,
        private readonly ShowNewsHandler $show_news_handler,
    ) {
    }

    public function store(CreateNewsRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $news_data = $this->create_news_handler->handle($dto);

        return response()->json($news_data->toArray(), 201);
    }

    public function show(int $id, ShowNewsRequest $request): JsonResponse
    {
        $pagination_dto = $request->toDto();
        $data = $this->show_news_handler->handle($id, $pagination_dto);

        return response()->json($data);
    }
}
