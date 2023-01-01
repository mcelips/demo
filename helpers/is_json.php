<?php

if (!function_exists('is_json')) {
    /**
     * Проверяет строку на соответствие с маской закодированного JSON массива
     *
     * @param $string
     *
     * @return bool
     */
    function is_json($string)
    {
        return is_string($string)
            && is_array(json_decode($string, true))
            && (json_last_error() == JSON_ERROR_NONE);
    }
}