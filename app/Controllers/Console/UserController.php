<?php

namespace App\Controllers\Console;

use App\Components\User\Errors\UserError;
use App\Components\User\User;
use App\Components\User\Validation\UserValidate;
use App\Controllers\Controller;

class UserController extends Controller
{

    protected $title          = 'Пользователи';
    protected $templateFolder = 'user';

    protected $model;

    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Общий список
     *
     * @return void
     */
    public function index()
    {
        $title = $this->title;

        $users = $this->model->get();

        render(
            $this->templateFolder . '.index',
            compact(
                'title',
                'users'
            ),
            'default',
            'console'
        );
    }

    /**
     * Форма добавления
     *
     * @return void
     */
    public function create()
    {
        $title    = $this->title . ' / Создание';
        $user = [];
        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'user'
            ),
            'default',
            'console'
        );
    }

    /**
     * Добавляем данные
     *
     * @return void
     */
    public function store()
    {
        // добавляем данные
        $create_status = $this->model->insert(UserValidate::validate());

        // если данные не добавились
        if (! $create_status) {
            validate_error_and_die('Не удалось добавить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные добавлены',
            route('console.users'),
            route('console.users.edit', ['id' => $create_status])
        );
    }

    /**
     * Форма редактирования
     *
     * @return void
     */
    public function edit()
    {
        $id = (int)from_get('id');

        $title = $this->title . ' / Редактирование';

        $user = $this->model->where('id', $id)->getOne();

        if (! $user) {
            validate_error_and_die(UserError::notFound());
        }

        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'user'
            ),
            'default',
            'console'
        );
    }

    /**
     * Обновляем данные
     *
     * @return void
     */
    public function update()
    {
        $id = (int)from_post('id');

        // ищем данные
        $user = $this->model->where('id', $id)->getOne();

        // проверяем данных
        if (! $user) {
            validate_error_and_die(UserError::notFound());
        }

        // обновляем данные
        $update_status = $this->model->where('id', $id)
                                     ->update(UserValidate::validate());

        // если данные не обновились
        if (! $update_status) {
            validate_error_and_die('Не удалось обновить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные обновлены',
            route('console.users')
        );
    }

    /**
     * Удаляем данные
     *
     * @return void
     */
    public function destroy()
    {
        $id = (int)from_get('id');

        if ($id === 1) {
            validate_error_and_die('Нельзя удалить данного пользователя.');
        }

        $delete_result = (bool)$this->model->where('id', $id)->delete();

        // если данные не обновились
        if (! $delete_result) {
            validate_error_and_die('Не удалось удалить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные удалены',
            route('console.users')
        );
    }
}