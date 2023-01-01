<?php

namespace App\Components\Auth\Services;

class PasswordService
{
    const PASSWORD_MIN_LENGTH = 8;

    /**
     * Проверка пароля
     *
     * @param string $password
     * @param string|null $confirm_password
     *
     * @return string
     */
    public static function check($password, $confirm_password = null)
    {
        // обрабатываем пароль
        $password = trim($password);

        // проверка длины пароля
        if (mb_strlen($password, 'UTF-8') < self::PASSWORD_MIN_LENGTH) {
            validate_error_and_die(
                __t(
                    'Минимальная длина пароля %length% символов.',
                    ['length' => self::PASSWORD_MIN_LENGTH]
                )
            );
        }

        // сравниваем пароли
        if ($confirm_password !== null) {
            if ($password !== $confirm_password) {
                validate_error_and_die('Пароли не совпадают.');
            }
        }

        return $password;
    }

    /**
     * Шифрование пароля
     *
     * @param string $password
     *
     * @return string
     */
    public static function hash($password)
    {
        return md5($password);
    }
}