<?php

namespace App\Http\Controllers\Api\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CommentList",
    type: "object",
    properties: [
        new OA\Property(
            property: "data",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Comment")
        ),
        new OA\Property(property: "limit", type: "integer", example: 10),
        new OA\Property(property: "next_cursor", type: "integer", nullable: true, example: 15, description: "ID последнего комментария для следующей страницы"),
        new OA\Property(property: "has_more", type: "boolean", example: true),
    ]
)]
class CommentListSchema {}
