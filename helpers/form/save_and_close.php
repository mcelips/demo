<?php

/**
 * Проверяет нажата ли кнопка "submit"
 *
 * @return bool
 * @version 0.101
 */
function save_clicked()
{
    return (bool)isset($_POST['submit']);
}


/**
 * Проверяет нажата ли кнопка "submit_close" или просто "submit"
 *
 * @return bool
 * @version 0.101
 */
function save_and_close()
{
    return (bool)isset($_POST['submit_close']);
}
