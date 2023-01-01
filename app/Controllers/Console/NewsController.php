<?php

namespace App\Controllers\Console;

use App\Components\News\Errors\NewsError;
use App\Components\News\News;
use App\Components\News\Validation\NewsValidate;
use App\Controllers\Controller;

class NewsController extends Controller
{

    protected $title          = 'Новости';
    protected $templateFolder = 'news';

    protected $model;

    public function __construct()
    {
        $this->model = new News();
    }

    /**
     * Общий список
     *
     * @return void
     */
    public function index()
    {
        $title = $this->title;

        $news = $this->model->get();

        render(
            $this->templateFolder . '.index',
            compact(
                'title',
                'news'
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
        $newsItem = [];
        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'newsItem'
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
        $create_status = $this->model->insert(NewsValidate::validate());

        // если данные не добавились
        if (! $create_status) {
            validate_error_and_die('Не удалось добавить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные добавлены',
            route('console.news'),
            route('console.news.edit', ['id' => $create_status])
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

        $newsItem = $this->model->where('id', $id)->getOne();

        if (! $newsItem) {
            validate_error_and_die(NewsError::notFound());
        }

        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'newsItem'
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
        $newsItem = $this->model->where('id', $id)->getOne();

        // проверяем данных
        if (! $newsItem) {
            validate_error_and_die(NewsError::notFound());
        }

        // обновляем данные
        $update_status = $this->model->where('id', $id)
                                     ->update(NewsValidate::validate());

        // если данные не обновились
        if (! $update_status) {
            validate_error_and_die('Не удалось обновить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные обновлены',
            route('console.news')
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

        $news = $this->model->where('id', $id)->getOne();

        if ($news) {
            $dir = STORAGE_PATH . '/news/';
            @unlink($dir . $news['image_thumb']);
            @unlink($dir . $news['image']);
        }

        $delete_result = (bool)$this->model->where('id', $id)->delete();

        // если данные не обновились
        if (! $delete_result) {
            validate_error_and_die('Не удалось удалить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные удалены',
            route('console.news')
        );
    }
}