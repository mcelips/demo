<?php

/**
 * Записывает ошибки валидации
 *
 * @param $errors
 *
 * @version 0.101
 */
function validate_error($errors)
{
    // Записываем сообщения об ошибках в сессию
    render_set_error($errors);

    // Записываем в сессию, что данные не прошли валидацию
    $_SESSION['validated'] = false;
}

/**
 * Записываем ошибку и возвращает на предыдущую страницу
 *
 * @param string $message
 *
 * @return void
 *
 * @version 0.512
 */
function validate_error_and_die($message)
{
    throw new \InvalidArgumentException($message);
}

/**
 * Записываем ошибку и возвращает на предыдущую страницу
 *
 * @param string      $message            Сообщение для уведомления
 * @param string|null $uri_save_and_close Перенаправление при нажатии кнопки "Сохранить и закрыть" (необязательно)
 * @param string|null $uri_save           Перенаправление при нажатии кнопки "Сохранить" (необязательно)
 *
 * @version 0.824
 */
function validate_success_and_die($message, $uri_save_and_close = null, $uri_save = null)
{
    // если AJAX, отдаем ошибку в JSON
    if (is_ajax()) {
        $redirect_uri = false;

        // если нажата кнопка "сохранить"
        if (save_clicked() and $uri_save !== null) {
            $redirect_uri = $uri_save;
        }

        // если нажата кнопка "сохранить и закрыть"
        if (save_and_close() and $uri_save_and_close !== null) {
            $redirect_uri = $uri_save_and_close;
        }

        // если указана ссылка для редиректа
        if ($redirect_uri) {
            render_set_success($message);
            json_response($redirect_uri);
        }

        // иначе возвращаем сообщение
        json_response($message);
    }

    // сообщение для уведомления
    render_set_success($message);

    // редирект при нажатии кнопки "Сохранить и закрыть"
    if (save_and_close() === true and $uri_save_and_close !== null) {
        redirect($uri_save_and_close);
    }

    // редирект при нажатии кнопки "Сохранить"
    if ($uri_save !== null) {
        redirect($uri_save);
    }

    redirect_back();
}
