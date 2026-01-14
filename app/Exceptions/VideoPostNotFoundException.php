<?php

namespace App\Exceptions;

use Exception;

class VideoPostNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Видео пост с ID {$id} не найден", 404);
    }
}
