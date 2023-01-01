<?php

use App\Components\Auth\Services\AuthService;
use App\Components\User\User;

/**
 * Пользователь не авторизован
 *
 * @return bool
 */
function is_guest()
{
    return AuthService::getAuthorizedUserData() === null;
}

/**
 * Пользователь авторизован
 *
 * @return bool
 */
function is_authorized()
{
    return is_guest() === false;
}

/**
 * Пользователь является админом
 *
 * @return bool
 */
function is_admin()
{
    return (is_authorized() === true and (int)user('status') === User::STATUS_ADMIN);
}
