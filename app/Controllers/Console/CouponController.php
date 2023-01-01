<?php

namespace App\Controllers\Console;

use App\Components\Coupon\Errors\CouponError;
use App\Components\Coupon\Coupon;
use App\Components\Coupon\Validation\CouponValidate;
use App\Controllers\Controller;

class CouponController extends Controller
{

    protected $title          = 'Купоны';
    protected $templateFolder = 'coupon';

    protected $model;

    public function __construct()
    {
        $this->model = new Coupon();
    }

    /**
     * Общий список
     *
     * @return void
     */
    public function index()
    {
        $title = $this->title;

        $coupons = $this->model->get();

        render(
            $this->templateFolder . '.index',
            compact(
                'title',
                'coupons'
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
        $title  = $this->title . ' / Создание';
        $coupon = [];
        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'coupon'
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
        $create_status = $this->model->insert(CouponValidate::validate());

        // если данные не добавились
        if (! $create_status) {
            validate_error_and_die('Не удалось добавить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные добавлены',
            route('console.coupons'),
            route('console.coupons.edit', ['id' => $create_status])
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

        $coupon = $this->model->where('id', $id)->getOne();

        if (! $coupon) {
            validate_error_and_die(CouponError::notFound());
        }

        render(
            $this->templateFolder . '.form',
            compact(
                'title',
                'coupon'
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
        $coupon = $this->model->where('id', $id)->getOne();

        // проверяем данных
        if (! $coupon) {
            validate_error_and_die(CouponError::notFound());
        }

        // обновляем данные
        $update_status = $this->model->where('id', $id)
                                     ->update(CouponValidate::validate());

        // если данные не обновились
        if (! $update_status) {
            validate_error_and_die('Не удалось обновить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные обновлены',
            route('console.coupons')
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

        $coupon = $this->model->where('id', $id)->getOne();

        if ($coupon) {
            $dir = STORAGE_PATH . '/coupons/';
            @unlink($dir . $coupon['image_thumb']);
            @unlink($dir . $coupon['image']);
        }

        $delete_result = (bool)$this->model->where('id', $id)->delete();

        // если данные не обновились
        if (! $delete_result) {
            validate_error_and_die('Не удалось удалить данные. Повторите позже.');
        }

        // ответ клиенту
        validate_success_and_die(
            'Данные удалены',
            route('console.coupons')
        );
    }
}