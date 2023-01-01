<?php

/**
 * Генератор хеша сессии
 *
 * @param int $length
 *
 * @return string
 */
function generate_session($length = 32)
{
    return substr(
        str_shuffle(
            str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                (int)ceil($length / strlen($x))
            )
        ), 1, $length);
}
