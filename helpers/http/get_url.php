<?php

/**
 * Формирует URL на основе данные $_SERVER
 *
 * @param string|null $postfix
 *
 * @return string
 */
function get_url($postfix = null)
{
    $url = (isset($_SERVER['HTTP_HOST']))
        ? ((isset($_SERVER['HTTPS']) and ! empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
        : '';

    $url = rtrim($url, '/');

    if ($postfix !== null and ! empty($postfix)) {
        return $url . '/' . ltrim($postfix, '/');
    }

    return $url;
}
