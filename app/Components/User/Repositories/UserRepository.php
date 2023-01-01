<?php

namespace App\Components\User\Repositories;

use App\Components\User\User;

class UserRepository
{

    /**
     * Возвращает данные игрока по логину и паролю.
     *
     * @param string $username
     * @param string $password
     * @param array  $columns
     *
     * @return array|null
     */
    public static function getByUsernameAndPassword(
        $username,
        $password,
        $columns = ['*']
    )
    {
        return (new User)->select($columns)
                         ->where('username', $username)
                         ->where('password', $password)
                         ->getOne();
    }

}