<?php

namespace App\Components\User\Errors;

class UserError
{

    /**
     * Ошибка - Пользователь не найден
     *
     * @return string
     */
    public static function notFound()
    {
        return 'Пользователь не найден.';
    }

}