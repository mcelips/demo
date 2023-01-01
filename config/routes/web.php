<?php

/**
 * Список маршрутов.
 *
 * имя маршрута, URL, путь к исполняемому файлу, метод
 */

use App\Controllers\Web\AuthController;
use App\Services\Router;

$router = Router::getInstance();

$router->get('home', '/', function () {
    if (is_guest()) {
        redirect(route('login'));
    } else {
        redirect(route('console'));
    }
});

if (is_guest()) {
    $router->get('login', '/login/', [AuthController::className(), 'login']);
    $router->post('login', '/login/', [AuthController::className(), 'loginPost']);
} else {
    $router->get('logout', '/logout/', [AuthController::className(), 'logout']);
}
