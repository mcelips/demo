<?php

namespace App\Components\User\Services;

use App\Components\Auth\Services\AuthService;
use App\Components\User\Repositories\UserRepository;
use App\Components\User\User;

class UserService
{

    /**
     * Авторизация по Username
     *
     * @param string $username
     * @param string $password
     * @param bool   $remember
     *
     * @return void
     */
    public static function loginByUsername(
        $username,
        $password,
        $remember = false
    )
    {
        self::login(UserRepository::getByUsernameAndPassword($username, $password), $remember);
    }

    /**
     * Аутентификация пользователя
     *
     * @param array|null $user
     * @param bool       $remember
     *
     * @return void
     */
    protected static function login($user, $remember)
    {
        // логин или пароль неверные
        if (! $user) {
            validate_error_and_die('Неверный логин или пароль');
        }

        // получаем токен сессии
        $session = self::getSessionToken($user);

        // сохраняем токен сессии
        AuthService::saveTokenToSession($session);

        // запоминаем пользователя
        if ($remember) {
            // записывает куки на неделю
            AuthService::saveTokenToCookie($session, AuthService::MAX_SESSION_LIFE);
        }
    }

    /**
     * Формирует токен сессии
     *
     * @param array|null $user
     *
     * @return string
     */
    public static function getSessionToken($user)
    {
        // сколько прошло с последнего входа
        $last_login_time_left = TMR - $user['last_login_tmr'];

        // максимальное время жизни сессии для админа (в секундах)
        $max_session_life = 3600 * 24 * 7; // 7 дней

        // если авторизуется админ, сессия в БД не пуста и время жизни сессии не истекло
        if (User::STATUS_ADMIN === $user['status'] and ! empty($user['session']) and $last_login_time_left < $max_session_life
        ) {
            // берем сессию админа
            return $user['session'];
        }

        // генерируем токен авторизации
        $session = md5(generate_session()) . TMR;

        // сохраняем токен в базе данных
        (new User())->where('id', $user['id'])
                    ->update([
                        'session'        => $session,
                        'last_login_tmr' => TMR,
                    ]);

        return $session;
    }

}