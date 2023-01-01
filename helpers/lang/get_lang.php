<?php

/**
 * Получает выбранный язык из сессии и возвращает его.
 *
 * @return string
 * @version 0.923
 */
function get_lang()
{
    $default_lang = config('lang.default');

    $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : $default_lang;

    if (! in_array($lang, config('lang.allowed'))) {
        $lang = $default_lang;
    }

    return (string)$lang;
}