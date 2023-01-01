<?php

namespace App\Components\News\Errors;

class NewsError
{


    /**
     * Ошибка - Новость не найдена
     *
     * @return string
     */
    public static function notFound()
    {
        return 'Новость не найдена.';
    }

}