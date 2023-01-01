<?php

/**
 * Редирект
 *
 * @param string $path     адрес, на который необходимо перейти
 * @param int    $response код ответа
 */
function redirect($path = '/', $response = 200)
{
    http_response_code($response);
    header('Location: ' . $path);
    exit;
}

/**
 * Редирект на предыдущую страницу
 *
 * @param int $status
 */
function redirect_back($status = 302)
{
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    redirect($referrer, $status);
}