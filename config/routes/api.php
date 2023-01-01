<?php

use App\Controllers\Api\CompetitionController;
use App\Controllers\Api\MainController;
use App\Services\Router;

$router = Router::getInstance();

$router->post('api.coupons', '/api/coupons', [MainController::class, 'coupons']);
$router->get('api.magazines', '/api/magazines', [MainController::class, 'magazines']);
$router->post('api.news', '/api/news', [MainController::class, 'news']);

$router->post('api.get_pdf_thumb', '/api/get_pdf_thumb', [MainController::class, 'get_pdf_thumb']);

// конкурсы
$router->get('api.competitions', '/api/competitions', [CompetitionController::class, 'all']);
$router->post('api.competitions.form', '/api/competitions/form', [CompetitionController::class, 'form']);
