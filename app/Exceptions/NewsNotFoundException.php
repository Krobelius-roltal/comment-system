<?php

namespace App\Exceptions;

use Exception;

class NewsNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Новость с ID {$id} не найдена", 404);
    }
}
