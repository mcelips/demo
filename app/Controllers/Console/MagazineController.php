<?php

namespace App\Controllers\Console;

use App\Components\Magazine\Errors\NewsError;
use App\Components\Magazine\Magazine;
use App\Components\Magazine\Validation\MagazineValidate;
use App\Controllers\Controller;

class MagazineController extends Controller
{

    protected $title          = 'Журналы';
    protected $templateFolder = 'magazine';

    protected $model;

    public function __construct()
    {
        $this->model = new Magazine();
    }

    /**
     * Общий список
     *
     * @return void
     */
    public function index()
    {
        $title = $this->title;

        $magazines = $this->model->get();

        render(
            $this->templateFolder . '.index',
            compact(
                'title',
                'magazines'
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
        $magazine = [];
        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'magazine'
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
        $create_status = $this->model->insert(MagazineValidate::validate());

        // если данные не добавились
        if (! $create_status) {
            validate_error_and_die('Не удалось добавить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные добавлены',
            route('console.magazines'),
            route('console.magazines.edit', ['id' => $create_status])
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

        $magazine = $this->model->where('id', $id)->getOne();

        if (! $magazine) {
            validate_error_and_die(NewsError::notFound());
        }

        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'magazine'
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
        $magazine = $this->model->where('id', $id)->getOne();

        // проверяем данных
        if (! $magazine) {
            validate_error_and_die(NewsError::notFound());
        }

        // обновляем данные
        $update_status = $this->model->where('id', $id)
                                     ->update(MagazineValidate::validate());

        // если данные не обновились
        if (! $update_status) {
            validate_error_and_die('Не удалось обновить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные обновлены',
            route('console.magazines')
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

        $magazine = $this->model->where('id', $id)->getOne();

        if ($magazine) {
            // удаляем файлы
            remove_dir(STORAGE_PATH . DS . $magazine['storage_path']);
        }

        $delete_result = (bool)$this->model->where('id', $id)->delete();

        // если данные не обновились
        if (! $delete_result) {
            validate_error_and_die('Не удалось удалить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные удалены',
            route('console.magazines')
        );
    }
}