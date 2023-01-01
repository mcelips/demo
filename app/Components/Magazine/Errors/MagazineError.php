<?php

namespace App\Components\Magazine\Errors;

class MagazineError
{


    /**
     * Ошибка - Журнал не найден
     *
     * @return string
     */
    public static function notFound()
    {
        return 'Журнал не найден.';
    }

}