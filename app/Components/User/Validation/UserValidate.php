<?php

namespace App\Components\User\Validation;

use App\Components\Auth\Services\PasswordService;
use App\Components\User\User;

class UserValidate
{

    public static function validate()
    {
        $result = [
            'username' => trim(strip_tags(from_post('username'))),
            'password' => from_post('password'),
            'status'   => User::STATUS_ADMIN,
        ];

        $id = (int)from_post('id');

        // запрещаем смену логина основного аккаунта
        if ($id and $id === 1) {
            unset($result['username']);
        } else {
            // проверяем логин
            if (empty($result['username'])) {
                validate_error_and_die('Логин не может быть пустым');
            }

            // экземпляр модели пользователей
            $user = new User();

            // проверяем логин на уникальность
            $username_check = ($id)
                ? $user->where('id', '!=', $id)->where('username', $result['username'])->count()
                : $user->where('username', $result['username'])->sum();

            if ((bool)$username_check) {
                validate_error_and_die('Пользователь с таким логином уже существует.');
            }
        }

        // проверяем пароль
        if ($id and ! $result['password']) {
            unset($result['password']);
        } else {
            $result['password'] = PasswordService::hash(PasswordService::check($result['password']));
        }

        return $result;
    }

}