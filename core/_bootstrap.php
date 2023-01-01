<?php
/**
 * Основной файл программы, который вызывается во всех остальных файлах.
 *
 * Подключаем файл с конфигурационными данными.
 * Устанавливаем соединение с базой данных.
 * Подключаем все необходимые файлы (вывод ошибок, вспомогательные функции...)
 *
 * Разрешено подключать файлы проекта, которые содержат дополнительную логику и/или расширение функционала для проекта.
 */

// Проверка версии PHP минимальным требованиям
if ((float)phpversion() < 5.4) {
    echo '<div style="text-align: center"><h1>ERROR!</h1><br>Minimum PHP version required 5.4.<br>Current version PHP: ' . number_format((float)phpversion(), 1) . '</div>';
    exit();
}

// Сокращенное название для DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);

// Корневая директория
define('ROOT', dirname(__DIR__));

// Текущее время
define('TMR', time());

// Подключаем загрузчик вспомогательных функций
require_once '_helpers.php';

// Загружаем все вспомогательные функции
helpers(['*']);

// Подключаем загрузчик вспомогательных функций
require_once slash_to_directory_separator(ROOT . '/config/constants.php');

// Подключаем composer autoload
if (file_exists(ROOT . '/vendor/autoload.php')) {
    require_once ROOT . '/vendor/autoload.php';
}

// Флаг разработки
define('DEBUG', config('app.debug'));

// отображение ошибок
if (DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Устанавливаем соединение с базой данных
$db_cfg = config('database.default');

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = mysqli_connect($db_cfg['host'], $db_cfg['username'], $db_cfg['password'], $db_cfg['database']) or json_response_error('DataBase connect Error!');
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    log_error($exception->getMessage());
    if (is_ajax()) {
        json_response_error('Server error');
    }
    echo 'Server error';
    exit();
}
mysqli_query($connection, "set names utf8");
