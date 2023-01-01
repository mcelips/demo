<?php

namespace App\Controllers\Console;

use App\Components\Competition\Errors\CompetitionError;
use App\Components\Competition\Competition;
use App\Components\Competition\Validation\CompetitionValidate;
use App\Controllers\Controller;

class CompetitionController extends Controller
{

    protected $title          = 'Конкурсы';
    protected $templateFolder = 'competition';

    protected $model;

    public function __construct()
    {
        $this->model = new Competition();
    }

    /**
     * Общий список
     *
     * @return void
     */
    public function index()
    {
        $title = $this->title;

        $competitions = $this->model->get();

        render(
            $this->templateFolder . '.index',
            compact(
                'title',
                'competitions'
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
        $competition = [];
        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'competition'
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
        $create_status = $this->model->insert(CompetitionValidate::validate());

        // если данные не добавились
        if (! $create_status) {
            validate_error_and_die('Не удалось добавить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные добавлены',
            route('console.competitions'),
            route('console.competitions.edit', ['id' => $create_status])
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

        $competition = $this->model->where('id', $id)->getOne();

        if (! $competition) {
            validate_error_and_die(CompetitionError::notFound());
        }

        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'competition'
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
        $competition = $this->model->where('id', $id)->getOne();

        // проверяем данных
        if (! $competition) {
            validate_error_and_die(CompetitionError::notFound());
        }

        // обновляем данные
        $update_status = $this->model->where('id', $id)
                                     ->update(CompetitionValidate::validate());

        // если данные не обновились
        if (! $update_status) {
            validate_error_and_die('Не удалось обновить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные обновлены',
            route('console.competitions')
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

        $competition = $this->model->where('id', $id)->getOne();

        if ($competition) {
            $dir = STORAGE_PATH . '/competitions/';
            @unlink($dir . $competition['image_thumb']);
            @unlink($dir . $competition['image']);
        }

        $delete_result = (bool)$this->model->where('id', $id)->delete();

        // если данные не обновились
        if (! $delete_result) {
            validate_error_and_die('Не удалось удалить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные удалены',
            route('console.competitions')
        );
    }
}