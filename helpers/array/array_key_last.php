<?php


if (! function_exists('array_key_last')) {
    /**
     * Получает первый ключ массива
     *
     * @param array $array
     *
     * @return int|string|null
     */
    function array_key_last(array $array)
    {
        if (empty($array)) {
            return null;
        }
        end($array);

        return key($array);
    }
}