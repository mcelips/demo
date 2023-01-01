<?php

/**
 * Возвращает сообщения в зависимости от выбранного языка.
 *
 * @param string      $id           Текст сообщения (является ключом для поиска в словаре)
 * @param array       $parameters   Параметры для подстановки данных в сообщение
 * @param string|null $locale       Язык. По умолчанию берется из функции get_lang()
 * @param array       $translations Пользовательский словарь переводов. По умолчанию словарь загружается из папки
 *                                  /translations/
 *
 * @return string
 */
function __t(
    $id,
    $parameters = [],
    $locale = null,
    $translations = []
)
{
    if (empty($id)) {
        return '';
    }

    // текущий язык
    if (! $locale) {
        $locale = get_lang();
    }

    // ищем перевод в словаре от пользователя
    if (array_key_exists($locale, $translations)) {
        return $translations[$locale];
    }

    // путь к словарю
    $file_name = path_to_file(sprintf('/translations/%s.php', $locale));

    // если словарь не найден
    if (! $file_name) {
        return add_parameters_to_string($id, $parameters);
    }

    // получаем список сообщений из словаря
    $messages = require $file_name;

    // если не существует в словаре ключа
    if (! array_key_exists($id, $messages)) {
        return add_parameters_to_string($id, $parameters);
    }

    return add_parameters_to_string($messages[$id], $parameters);
}

/**
 * Добавляет данные из параметров в сообщение по ключу
 *
 * @param string $string
 * @param array  $parameters
 *
 * @return string
 */
function add_parameters_to_string($string, $parameters)
{
    // если параметры пусты
    if (empty($parameters)) {
        return $string;
    }

    // добавляем параметры в строку
    foreach ($parameters as $key => $value) {
        $string = str_replace("%$key%", (string)$value, $string);
    }

    return $string;
}
