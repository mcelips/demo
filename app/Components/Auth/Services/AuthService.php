<?php

namespace App\Components\Auth\Services;

use App\Components\User\User;

class AuthService
{
    /**
     * Максимальная жизнь сессии в секундах
     */
    const MAX_SESSION_LIFE = 604800;

    /**
     * Возвращает данные авторизованного пользователя, если не найден, то выдает ошибку о необходимости перезайти в игру
     *
     * @param string[] $columns
     *
     * @return array
     */
    public static function getUser($columns = ['*'])
    {
        $user = self::getAuthorizedUserData($columns);
        if (! $user) {
            validate_error_and_die('Ваша сессия истекла.');
        }

        return $user;
    }

    /**
     * Возвращает данные авторизованного пользователя
     *
     * @param string[] $columns
     *
     * @return array|null
     */
    public static function getAuthorizedUserData($columns = ['*'])
    {
        return (new User)->select($columns)
                         ->where('session', self::getTokenFromSession())
                         ->whereNotNull('session')
                         ->getOne();
    }

    /**
     * Возвращает токен из сессии
     *
     * @return string|null
     */
    public static function getTokenFromSession()
    {
        return isset($_SESSION['user']['token']) ? $_SESSION['user']['token'] : null;
    }

    /**
     * Сохраняет токен в сессию
     *
     * @param string $token
     */
    public static function saveTokenToSession($token)
    {
        $_SESSION['user']['token'] = $token;
    }

    /**
     * Проверяем авторизации токен в базе данных
     *
     * @param string|null $token
     *
     * @return bool
     */
    public static function tokenCheck($token = null)
    {
        return (bool)(new User)->where('session', $token)
                               ->whereNotNull('session')
                               ->count();
    }

    /**
     * Удаляем токен из COOKIE, сессии и базы данных
     */
    public static function authTokenDestroy()
    {
        $token = self::getToken();

        if ($token !== null) {
            self::saveTokenToCookie('', -1);
            self::destroySession();

            (new User)->where('session', $token)
                      ->update([
                          'session' => null,
                      ]);
        }
    }

    /**
     * Возвращает токен из сессии или COOKIE
     *
     * @return string|null
     */
    public static function getToken()
    {
        return self::getTokenFromSession() ? self::getTokenFromSession() : self::getTokenFromCookie();
    }

    /**
     * Возвращает токен из COOKIE
     *
     * @return string|null
     */
    public static function getTokenFromCookie()
    {
        return isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : null;
    }

    /**
     * Сохраняет токен в сессию
     *
     * @param string $token Токен
     * @param int    $time  Время жизни куки (по умолчанию 1 минута)
     */
    public static function saveTokenToCookie($token, $time = 60)
    {
        setcookie('auth_token', $token, time() + $time, '/');
    }

    /**
     * Удаляет данные из сессии
     */
    public static function destroySession()
    {
        unset($_SESSION['user']);
    }

}
