<?php


if (!function_exists('array_key_first')) {
    /**
     * Получает первый ключ массива
     *
     * @param array $array
     *
     * @return int|string|null
     */
    function array_key_first(array $array)
    {
        if (empty($array)) {
            return null;
        }
        foreach ($array as $key => $value) {
            return $key;
        }
        return null;
    }
}