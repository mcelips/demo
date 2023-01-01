<?php

/**
 * Возвращает полный путь к асетам
 *
 * @param string      $url
 * @param string|null $version
 *
 * @return string
 *
 * @version 0.710
 */
function asset_url($url, $version = null)
{
    $url = get_url(config('app.url_prefix')) . '/assets/' . ltrim($url, '/');

    if (
        $version === null or
        empty($version)
    ) {
        return $url;
    }

    return $url . '?ver=' . $version;
}
