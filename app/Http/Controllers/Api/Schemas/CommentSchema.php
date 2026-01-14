<?php

namespace App\Http\Controllers\Api\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Comment",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "commentable_type", type: "string", example: "App\\Models\\News"),
        new OA\Property(property: "commentable_id", type: "integer", example: 1),
        new OA\Property(property: "parent_id", type: "integer", nullable: true, example: null),
        new OA\Property(property: "content", type: "string", example: "Текст комментария"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
        new OA\Property(property: "user", ref: "#/components/schemas/User", nullable: true),
    ]
)]
class CommentSchema {}
