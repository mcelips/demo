<?php

/**
 * Значение ключа массива как номер элемента массива
 *
 * @param $array
 * @param $keyValue
 *
 * @return array
 */
function value_as_key($array, $keyValue)
{
    $newArr = [];

    // Если массив пуст, возвращаем пустой массив
    if (empty($array)) return $newArr;

    // Проходим по принятому массиву
    foreach ($array as $key => $item) {
        // Проверяем тип элемента массива
        if (is_object($item))
            $newArr[$item->$keyValue] = $item;
        else
            $newArr[$item[$keyValue]] = $item;
    }

    return $newArr;
}
