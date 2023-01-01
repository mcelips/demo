<?php

use App\Components\Auth\Services\AuthService;

/**
 * @param null $key
 *
 * @return mixed|string
 */
function user($key = null)
{
    // данные пользователя
    $user = AuthService::getAuthorizedUserData();

    if ($key === null) {
        return $user;
    }

    return isset($user[$key]) ? $user[$key] : false;
}
