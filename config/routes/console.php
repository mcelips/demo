<?php

use App\Controllers\Console\CompetitionController;
use App\Controllers\Console\CouponController;
use App\Controllers\Console\DashboardController;
use App\Controllers\Console\MagazineController;
use App\Controllers\Console\NewsController;
use App\Controllers\Console\UserController;
use App\Services\Router;

$router = Router::getInstance();

if (is_admin()) {
    $router->get('console', '/console', [DashboardController::className(), 'index']);

    // конкурсы
    $router->get('console.competitions', '/console/competitions', [CompetitionController::className(), 'index']);
    $router->get(
        'console.competitions.create',
        '/console/competitions/create',
        [CompetitionController::className(), 'create']
    );
    $router->post(
        'console.competitions.create',
        '/console/competitions/create',
        [CompetitionController::className(), 'store']
    );
    $router->get('console.competitions.edit', '/console/competitions/edit', [CompetitionController::className(), 'edit']
    );
    $router->post(
        'console.competitions.edit',
        '/console/competitions/edit',
        [CompetitionController::className(), 'update']
    );
    $router->get(
        'console.competitions.delete',
        '/console/competitions/delete',
        [CompetitionController::className(), 'destroy']
    );

    // соревнования
    $router->get('console.coupons', '/console/coupons', [CouponController::className(), 'index']);
    $router->get('console.coupons.create', '/console/coupons/create', [CouponController::className(), 'create']);
    $router->post('console.coupons.create', '/console/coupons/create', [CouponController::className(), 'store']);
    $router->get('console.coupons.edit', '/console/coupons/edit', [CouponController::className(), 'edit']);
    $router->post('console.coupons.edit', '/console/coupons/edit', [CouponController::className(), 'update']);
    $router->get('console.coupons.delete', '/console/coupons/delete', [CouponController::className(), 'destroy']);

    // журналы
    $router->get('console.magazines', '/console/magazines', [MagazineController::className(), 'index']);
    $router->get('console.magazines.create', '/console/magazines/create', [MagazineController::className(), 'create']);
    $router->post('console.magazines.create', '/console/magazines/create', [MagazineController::className(), 'store']);
    $router->get('console.magazines.edit', '/console/magazines/edit', [MagazineController::className(), 'edit']);
    $router->post('console.magazines.edit', '/console/magazines/edit', [MagazineController::className(), 'update']);
    $router->get('console.magazines.delete', '/console/magazines/delete', [MagazineController::className(), 'destroy']);

    // журналы
    $router->get('console.news', '/console/news', [NewsController::className(), 'index']);
    $router->get('console.news.create', '/console/news/create', [NewsController::className(), 'create']);
    $router->post('console.news.create', '/console/news/create', [NewsController::className(), 'store']);
    $router->get('console.news.edit', '/console/news/edit', [NewsController::className(), 'edit']);
    $router->post('console.news.edit', '/console/news/edit', [NewsController::className(), 'update']);
    $router->get('console.news.delete', '/console/news/delete', [NewsController::className(), 'destroy']);

    // пользователи
    $router->get('console.users', '/console/users', [UserController::className(), 'index']);
    $router->get('console.users.create', '/console/users/create', [UserController::className(), 'create']);
    $router->post('console.users.create', '/console/users/create', [UserController::className(), 'store']);
    $router->get('console.users.edit', '/console/users/edit', [UserController::className(), 'edit']);
    $router->post('console.users.edit', '/console/users/edit', [UserController::className(), 'update']);
    $router->get('console.users.delete', '/console/users/delete', [UserController::className(), 'destroy']);
}
