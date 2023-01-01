<?php

namespace App\Components\Coupon\Errors;

class CouponError
{


    /**
     * Ошибка - Купон не найден
     *
     * @return string
     */
    public static function notFound()
    {
        return 'Купон не найден.';
    }

}