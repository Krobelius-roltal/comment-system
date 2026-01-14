<?php

namespace App\Http\Controllers\Api;

use App\Handlers\Comment\CreateCommentHandler;
use App\Handlers\Comment\DeleteCommentHandler;
use App\Handlers\Comment\IndexCommentHandler;
use App\Handlers\Comment\ShowCommentHandler;
use App\Handlers\Comment\UpdateCommentHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\IndexCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    public function __construct(
        private readonly IndexCommentHandler $index_comment_handler,
        private readonly CreateCommentHandler $create_comment_handler,
        private readonly ShowCommentHandler $show_comment_handler,
        private readonly UpdateCommentHandler $update_comment_handler,
        private readonly DeleteCommentHandler $delete_comment_handler,
    ) {
    }

    #[OA\Get(
        path: "/comments",
        summary: "Получение списка комментариев",
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(
                name: "commentable_type",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", enum: ["news", "video-post"])
            ),
            new OA\Parameter(
                name: "commentable_id",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "parent_id",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", nullable: true)
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
                content: new OA\JsonContent(ref: "#/components/schemas/CommentList")
            ),
            new OA\Response(response: 422, description: "Ошибка валидации"),
        ]
    )]
    public function index(IndexCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $data = $this->index_comment_handler->handle($dto);

        return response()->json($data);
    }

    #[OA\Post(
        path: "/comments",
        summary: "Создание комментария",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 1),
                    new OA\Property(property: "commentable_type", type: "string", enum: ["news", "video-post"], example: "news"),
                    new OA\Property(property: "commentable_id", type: "integer", example: 1),
                    new OA\Property(property: "parent_id", type: "integer", example: null, nullable: true),
                    new OA\Property(property: "content", type: "string", example: "Текст комментария"),
                ]
            )
        ),
        tags: ["Comments"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Комментарий создан",
                content: new OA\JsonContent(ref: "#/components/schemas/Comment")
            ),
            new OA\Response(response: 422, description: "Ошибка валидации"),
        ]
    )]
    public function store(CreateCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $comment_data = $this->create_comment_handler->handle($dto);

        return response()->json($comment_data->toArray(), 201);
    }

    #[OA\Get(
        path: "/comments/{id}",
        summary: "Получение комментария",
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(ref: "#/components/schemas/Comment")
            ),
            new OA\Response(response: 404, description: "Не найдено"),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $data = $this->show_comment_handler->handle($id);

        return response()->json($data);
    }

    #[OA\Put(
        path: "/comments/{id}",
        summary: "Обновление комментария",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "content", type: "string", example: "Обновленный текст комментария"),
                ]
            )
        ),
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Комментарий обновлен",
                content: new OA\JsonContent(ref: "#/components/schemas/Comment")
            ),
            new OA\Response(response: 404, description: "Не найдено"),
            new OA\Response(response: 422, description: "Ошибка валидации"),
        ]
    )]
    #[OA\Patch(
        path: "/comments/{id}",
        summary: "Обновление комментария",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "content", type: "string", example: "Обновленный текст комментария"),
                ]
            )
        ),
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Комментарий обновлен",
                content: new OA\JsonContent(ref: "#/components/schemas/Comment")
            ),
            new OA\Response(response: 404, description: "Не найдено"),
            new OA\Response(response: 422, description: "Ошибка валидации"),
        ]
    )]
    public function update(int $id, UpdateCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $comment_data = $this->update_comment_handler->handle($id, $dto);

        return response()->json($comment_data->toArray());
    }

    #[OA\Delete(
        path: "/comments/{id}",
        summary: "Удаление комментария",
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: "Комментарий удален"),
            new OA\Response(response: 404, description: "Не найдено"),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->delete_comment_handler->handle($id);

        return response()->json(null, 204);
    }
}
