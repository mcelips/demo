<?php

/**
 * Данные предыдущей формы из сессии.
 *
 * @param string       $key
 * @param array|object $data
 *
 * @return mixed|null
 * @version 0.101
 */
function old($key, $data = [])
{
    $post = null;

    if (isset($_SESSION['post']) === false and isset($data[$key]) === false) {
        return null;
    }

    if (isset($_SESSION['post'][$key]) === true) {
        $post = $_SESSION['post'][$key];
        unset($_SESSION['post'][$key]);
    }

    if (
        is_object($data) and
        isset($data->$key)
    ) {
        return $data->$key;
    }

    if (isset($data[$key]) === true) {
        return $data[$key];
    }

    return $post;
}