<?php

helpers([
    'files.path_to_file',
]);

$CONFIG_HELPER_DATA = [];

/**
 * Возвращает данные из конфигурационного файла по ключу
 *
 * @param null $key Если в качестве ключа передать несколько ключей через ".", то вернется значение из многомерного
 *                  массива. Например, 'app.name' вернет значение из массива $config['app']['name']
 *
 * @return false|mixed
 */
function config($key = null)
{
    global $CONFIG_HELPER_DATA;

    $config_file = path_to_file('config/config.php');

    if (empty($CONFIG_HELPER_DATA) and in_array($config_file, get_included_files()) === false) {
        $CONFIG_HELPER_DATA = require_once ROOT . '/config/config.php';
    }

    if ($key == null) {
        return $CONFIG_HELPER_DATA;
    }

    // разбиваем
    $keys = explode('.', $key);

    return config_get_element($CONFIG_HELPER_DATA, $keys);
}


/**
 * Retrieves an element within multidimensional array stored on any level by it's keys.
 *
 * @param array $data A multidimensional array with data
 * @param array $keys A list of keys to element stored in $data
 *
 * @return null|mixed Returns null if elements is not found. Element's value otherwise.
 */
function config_get_element(array $data, array $keys)
{
    /** перебираем ключи */
    foreach ($keys as $key) {
        /**
         * Если текущий элемент - массив, и в нём есть ключ, то текущий массив перезаписываем на новый.
         * А если ключа такого нет или это не массив, то возвращаем null.
         */
        if (is_array($data) && array_key_exists($key, $data)) {
            $data = $data[$key];
        } else {
            return null;
        }
    }

    return $data;
}