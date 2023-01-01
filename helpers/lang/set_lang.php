<?php

/**
 * Записываем выбранный язык в сессию.
 *
 * @param string|null $lang
 *
 * @version 0.923
 */
function set_lang($lang = null)
{
    // записываем выбранный язык в сессию
    $_SESSION['lang'] = in_array($lang, config('lang.allowed')) ? $lang : config('lang.default');
}