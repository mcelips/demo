<?php

use App\Services\Helpers;


/**
 * Ответ в JSON формате со статусом TRUE
 *
 * @param $message
 *
 * @version 0.101
 */
function json_response($message)
{
    send_response(true, prepare_response($message), __t('Success'));
}


/**
 * Ответ в JSON формате со статусом TRUE (информация)
 *
 * @param $message
 *
 * @version 0.101
 */
function json_response_info($message)
{
    send_response(true, prepare_response($message), __t('Info'));
}


/**
 * Ответ в JSON формате со статусом FALSE (ошибка)
 *
 * @param $message
 *
 * @version 0.101
 */
function json_response_error($message)
{
    send_response(false, prepare_response($message), __t('Error'));
}


/**
 * Ответ в JSON формате со статусом FALSE (предупреждение)
 *
 * @param $message
 *
 * @version 0.101
 */
function json_response_warning($message)
{
    send_response(false, prepare_response($message), __t('Warning'));
}


/**
 * Конвертируем сообщение в массив при необходимости
 *
 * @param $message
 *
 * @return array
 * @version 0.101
 */
function prepare_response($message)
{
    // явно указываем, что в ответ будем отдавать JSON
    header('Content-type: json/application');

    if (is_array($message) === true) {
        return Helpers::prepareArrayForResponse($message);
    }

    return ['message' => $message];
}


/**
 * Ответ в JSON без добавления статуса
 *
 * @param $message
 *
 * @version 0.101
 */
function json_api_response($message)
{
    // явно указываем, что в ответ будем отдавать JSON
    header('Content-type: json/application');
   die(json_encode(['messages' => $message], JSON_UNESCAPED_UNICODE));
}


/**
 * Формирует ответ.
 *
 * @param bool   $status
 * @param mixed  $message
 * @param string $title
 *
 * @version 0.101
 */
function send_response($status, $message, $title = 'Info!')
{
    // Если пришел JSON, конвертируем строку в массив
    if (is_json($message) === true) {
        $message = json_decode($message, true);
    }

    // Формируем ответ
    $response = [
        'status'  => $status,
        'message' => $message,
        'title'   => $title,
    ];

    // Выводим сообщение и прекращаем выполнение скрипта
    print_response($response);
}


/**
 * Выводит ответ в формате JSON, завершает выполнение скрипта.
 *
 * @param array $response
 *
 * @version 0.101
 */
function print_response(array $response)
{
    // Проверяем наличие поля статус
    $response['status'] = isset($response['status']) ? $response['status'] : false;

    $response = json_encode($response);

    // Добавляем квадратные кнопки для message при необходимости
    $response = str_replace('"message":{', '"message":[{', $response);
    $response = str_replace('},"title"', '}],"title"', $response);

    // Выводим сообщение и прекращаем выполнение скрипта
    die($response);
}
