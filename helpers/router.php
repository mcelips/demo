<?php

/**
 * Возвращает путь маршрута по ключу
 *
 * @param string $name
 * @param array  $params GET параметры
 *
 * @return string
 */
function route($name = '', $params = [])
{
    $router = \App\Services\Router::getInstance()->getByName($name);

    // Проверка существования маршрута по имени
    if (! $router) {
        validate_error_and_die('Router error! Route ' . $name . ' not found.');
    }

    // Получаем ссылку
    $route_link = get_url($router['link']);

    if (empty($params) === false) {
        // Добавляем в ссылку GET параметры
        $get_params = [];
        foreach ($params as $key => $value) {
            $get_params[] = "$key=$value";
        }
        $route_link .= '/?' . implode('&', $get_params);
    }

    return $route_link;
}
