<?php

/**
 * Шаблоны Bootstrap для сообщений
 *
 * @return string[]
 */
function get_message_templates()
{
    return [
        'error'   => '<div class="alert alert-danger">:text</div>',
        'info'    => '<div class="alert alert-info">:text</div>',
        'success' => '<div class= "alert alert-success alert-dismissible">:text <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>',
        'warning' => '<div class="alert alert-warning">:text <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>',
    ];
}

/**
 * Записываем сообщение в сессию
 *
 * @param array|string $text Текст сообщения
 * @param string       $type Тип сообщения
 */
function render_set_message($text, $type = 'info')
{
    // разрешенные типы
    $allowed_types = array_keys(get_message_templates());

    // Если пришел массив
    if (is_array($text)) {
        $text = implode('<br>', $text);
    }

    // Удаляем лишние пробелы
    $text = trim($text);


    // Если текст не пустой
    if (!empty($text)) {
        // Проверяем наличие шаблона оформления
        if (in_array($type, $allowed_types)) {
            // Если нет массива message, создаем
            if (isset($_SESSION['message']) === false) {
                $_SESSION['message'] = [];
            }

            // Записываем сообщение в сессию
            $_SESSION['message'][$type][] = $text;
        }
    }
}

/**
 * Записываем сообщение об успехе
 */
function render_set_success($text)
{
    render_set_message($text, 'success');
}

/**
 * Записываем сообщение предупреждения
 */
function render_set_warning($text)
{
    render_set_message($text, 'warning');
}

/**
 * Записываем сообщение об ошибке
 */
function render_set_error($text)
{
    render_set_message($text, 'error');
}

/**
 * Получаем сообщение из сессии
 *
 * @param bool $with_template
 *
 * @return string|array
 */
function render_get_message($with_template = true)
{
    $message = [];
    $message_templates = get_message_templates();

    if (isset($_SESSION['message']) and !empty($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $type => $text) {
            if ($with_template === false) {
                $message[$type] = $text;
            } else {
                $text = implode('<br>', $text);
                $message[] = str_replace(':text', $text, $message_templates[$type]);
            }
        }
    }

    unset($_SESSION['message']);

    if ($with_template === true) {
        return implode('', $message);
    }

    return $message;
}
