<?php

// Инициализируем сессию
session_start();

try {
    // подключаем загрузчик
    require_once '../core/_bootstrap.php';

    // подключаем маршруты
    require_once ROOT . '/config/routes/api.php';
    require_once ROOT . '/config/routes/console.php';
    require_once ROOT . '/config/routes/web.php';

    // запускаем маршрутизатор
    \App\Services\Router::getInstance()->run();
} catch (\InvalidArgumentException $e) {
    // если AJAX, отдаем ошибку в JSON
    if (is_ajax()) {
        json_response_error($e->getMessage());
    }

    // выводим ошибку в шаблон
    validate_error($e->getMessage());
    // в сессию сохраняем данные из массива $_POST
    $_SESSION['post'] = from_post();
    // редирект на главную
    redirect_back();
} catch (Exception $exception) {
    if (DEBUG) {
        dd($exception->getMessage());
    }
    http_response_code(500);
    die('Server error!');
}
