<?php

/**
 * Возвращает полный путь к файлу относительно корневой категории.
 *
 * @param string $file Имя файла. Для подключения файла из подпапки в качестве разделителя используйте точку. Пример,
 *                     значение 'debug.dump' подключит файл 'dump.php' из подпапки 'debug'.
 *
 * @return bool|string Полный путь файла, если он существует.
 */
function path_to_file($file)
{
    // Удаляем расширение .php
    $file_name = str_replace('.php', '', $file);

    // Заменяем . на DIRECTORY_SEPARATOR
    $file_name = str_replace('.', DS, $file_name) . '.php';

    // Удаляем слэши в начале и конце строки, заменяем слэш на DIRECTORY_SEPARATOR
    $file_name = str_replace('/', DS, trim($file_name, '\\/'));

    // Полный путь к файлу
    $file_path = ROOT . DS . $file_name;

    // Проверяем существует ли файл
    if (false === file_exists($file_path)) {
        return false;
    }

    // Возвращаем полный путь к файлу от корневой папки
    return $file_path;
}
