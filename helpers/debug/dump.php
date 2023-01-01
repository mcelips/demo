<?php

/**
 * Распечатываем массив в читабельном формате
 *
 * @param mixed $data
 * @param bool  $var_dump флаг распечатки через VAR_DUMP() вместо PRINT_R()
 */
function dump($data, $var_dump = false)
{
    echo '<pre style="background: #18171B; color: #fff3cd; font-size: 14px; padding: 10px; margin: 10px; white-space: pre-wrap; word-wrap: break-word;">';

    if (true === $var_dump) {
        var_dump($data);
    } else {
        echo print_r($data, true);
    }

    echo "</pre>";
}

/**
 * Выводит массив в читабельном формате через VAR_DUMP()
 *
 * @param $data
 *
 * @return void
 */
function vdump($data)
{
    dump($data, true);
}

/**
 * Распечатываем массив и прекращаем выполнение скрипта
 * dump() и exit()
 *
 * @param mixed $data
 * @param bool  $var_dump флаг распечатки через VAR_DUMP() вместо PRINT_R()
 */
function dd($data, $var_dump = false)
{
    dump($data, $var_dump);
    exit(1);
}

/**
 * Выводит массив в читабельном формате через VAR_DUMP() и прекращает выполнение скрипта
 *
 * @param $data
 *
 * @return void
 */
function vdd($data)
{
    dd($data, true);
}
