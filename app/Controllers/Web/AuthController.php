<?php

namespace App\Controllers\Web;

use App\Components\Auth\Services\AuthService;
use App\Components\Auth\Services\PasswordService;
use App\Components\User\Services\UserService;
use App\Controllers\Controller;

class AuthController extends Controller
{

    /**
     * Форма аутентификации
     *
     * @return void
     */
    public function login()
    {
        render('login', [], false, 'console');
    }


    /**
     * Аутентификация
     *
     * @return void
     */
    public function loginPost()
    {
        $username = from_post('username');
        $password = from_post('password');
        $remember = from_post('remember') === 'on';

        UserService::loginByUsername($username, PasswordService::hash($password), $remember);
        redirect(route('home'));
    }


    /**
     * Деаутентификация пользователя
     *
     * @return void
     */
    public function logout()
    {
        AuthService::authTokenDestroy();
        redirect(route('home'));
    }

}