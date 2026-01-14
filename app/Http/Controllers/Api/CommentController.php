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

    public function index(IndexCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $data = $this->index_comment_handler->handle($dto);

        return response()->json($data);
    }

    public function store(CreateCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $comment_data = $this->create_comment_handler->handle($dto);

        return response()->json($comment_data->toArray(), 201);
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->show_comment_handler->handle($id);

        return response()->json($data);
    }

    public function update(int $id, UpdateCommentRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $comment_data = $this->update_comment_handler->handle($id, $dto);

        return response()->json($comment_data->toArray());
    }

    public function destroy(int $id): JsonResponse
    {
        $this->delete_comment_handler->handle($id);

        return response()->json(null, 204);
    }
}
