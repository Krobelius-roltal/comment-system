<?php

namespace App\Exceptions;

use Exception;

class CommentNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Комментарий с ID {$id} не найден", 404);
    }
}
