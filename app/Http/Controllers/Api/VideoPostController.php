<?php

namespace App\Http\Controllers\Api;

use App\Handlers\VideoPost\CreateVideoPostHandler;
use App\Handlers\VideoPost\ShowVideoPostHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVideoPostRequest;
use App\Http\Requests\ShowVideoPostRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class VideoPostController extends Controller
{
    public function __construct(
        private readonly CreateVideoPostHandler $create_video_post_handler,
        private readonly ShowVideoPostHandler $show_video_post_handler,
    ) {
    }

    #[OA\Post(
        path: "/video-posts",
        summary: "Создание видео поста",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Название видео поста"),
                    new OA\Property(property: "description", type: "string", example: "Описание видео поста"),
                ]
            )
        ),
        tags: ["VideoPosts"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Видео пост создан",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Название видео поста"),
                        new OA\Property(property: "description", type: "string", example: "Описание видео поста"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Ошибка валидации"),
        ]
    )]
    public function store(CreateVideoPostRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $video_post_data = $this->create_video_post_handler->handle($dto);

        return response()->json($video_post_data->toArray(), 201);
    }

    #[OA\Get(
        path: "/video-posts/{id}",
        summary: "Получение видео поста с комментариями",
        tags: ["VideoPosts"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            ),
            new OA\Parameter(
                name: "cursor",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", nullable: true, description: "ID последнего комментария предыдущей страницы")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                        new OA\Property(
                            property: "comments",
                            properties: [
                                new OA\Property(
                                    property: "data",
                                    type: "array",
                                    items: new OA\Items(ref: "#/components/schemas/Comment")
                                ),
                                new OA\Property(property: "limit", type: "integer", example: 10),
                                new OA\Property(property: "next_cursor", type: "integer", nullable: true, example: 15, description: "ID последнего комментария для следующей страницы"),
                                new OA\Property(property: "has_more", type: "boolean", example: true),
                            ],
                            type: "object"
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Не найдено"),
        ]
    )]
    public function show(int $id, ShowVideoPostRequest $request): JsonResponse
    {
        $pagination_dto = $request->toDto();
        $data = $this->show_video_post_handler->handle($id, $pagination_dto);

        return response()->json($data);
    }
}
