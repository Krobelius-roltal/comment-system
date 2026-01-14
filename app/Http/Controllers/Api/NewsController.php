<?php

namespace App\Http\Controllers\Api;

use App\Handlers\News\CreateNewsHandler;
use App\Handlers\News\ShowNewsHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewsRequest;
use App\Http\Requests\ShowNewsRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API для системы комментариев к контенту",
    title: "Content System API"
)]
#[OA\Server(
    url: "/api/v1",
    description: "API v1"
)]
class NewsController extends Controller
{
    public function __construct(
        private readonly CreateNewsHandler $create_news_handler,
        private readonly ShowNewsHandler $show_news_handler,
    ) {
    }

    #[OA\Post(
        path: "/news",
        summary: "Создание новости",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Название новости"),
                    new OA\Property(property: "description", type: "string", example: "Описание новости"),
                ]
            )
        ),
        tags: ["News"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Новость создана",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Название новости"),
                        new OA\Property(property: "description", type: "string", example: "Описание новости"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time"),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Ошибка валидации"),
        ]
    )]
    public function store(CreateNewsRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $news_data = $this->create_news_handler->handle($dto);

        return response()->json($news_data->toArray(), 201);
    }

    #[OA\Get(
        path: "/news/{id}",
        summary: "Получение новости с комментариями",
        tags: ["News"],
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
    public function show(int $id, ShowNewsRequest $request): JsonResponse
    {
        $pagination_dto = $request->toDto();
        $data = $this->show_news_handler->handle($id, $pagination_dto);

        return response()->json($data);
    }
}
