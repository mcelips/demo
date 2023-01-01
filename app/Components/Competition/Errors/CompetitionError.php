<?php

namespace App\Components\Competition\Errors;

class CompetitionError
{


    /**
     * Ошибка - Конкурс не найден
     *
     * @return string
     */
    public static function notFound()
    {
        return 'Конкурс не найден.';
    }

}