<?php

/**
 * Формирует URL хоста на основе данные $_SERVER
 *
 * @return string
 */
function get_host()
{
    return (isset($_SERVER['HTTP_HOST']))
        ? $_SERVER['HTTP_HOST']
        : '';
}
