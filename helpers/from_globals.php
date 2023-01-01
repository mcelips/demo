<?php

/**
 * Получаем данные из массива $_POST по ключу и возвращаем подготовленные для SQL запроса данные.
 *
 * @param integer|string $key Ключ данных в массиве $_POST.
 *
 * @return false|string|array
 */
function from_post($key = null)
{
    global $connection;

    // Если пришел пустой ключ
    if ($key === null) {
        return $_POST;
    }

    // Если в массиве $_POST не существует такого ключа
    if (empty($key) or isset($_POST[$key]) === false) {
        return false;
    }

    return $_POST[$key];
}

/**
 * Получаем данные из массива $_GET по ключу и возвращаем подготовленные для SQL запроса данные.
 *
 * @param integer|string $key Ключ данных в массиве $_GET.
 *
 * @return false|string|array
 */
function from_get($key = null)
{
    global $connection;

    // Если пришел пустой ключ
    if ($key === null) {
        return $_GET;
    }

    // Если в массиве $_POST не существует такого ключа
    if (empty($key) or isset($_GET[$key]) === false) {
        return false;
    }

    return $_GET[$key];
}

/**
 * Получаем данные из массива $_FILES по ключу и возвращаем подготовленные для SQL запроса данные.
 *
 * @param integer|string $key Ключ данных в массиве $_FILES.
 *
 * @return false|string|array
 */
function from_files($key = null)
{
    // Если пришел пустой ключ
    if ($key === null) {
        return $_FILES;
    }

    // Если в массиве $_POST не существует такого ключа
    if (empty($key) or isset($_FILES[$key]) === false) {
        return false;
    }

    return $_FILES[$key];
}

/**
 * Получаем данные из массива по ключу и возвращаем подготовленные для SQL запроса данные.
 *
 * @param array          $array Массив с данными
 * @param integer|string $key   Ключ данных в массиве $_POST.
 *
 * @return false|string|array
 */
function from_array($array, $key)
{
    global $connection;

    // Если в массиве $_POST не существует такого ключа
    if (empty($key) or isset($array[$key]) === false) {
        return false;
    }

    return $array[$key];
}
