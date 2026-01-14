<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Пользователь с ID {$id} не найден", 404);
    }
}
