<?php

/**
 * Заменяет слэш на DIRECTORY_SEPARATOR
 *
 * @param string $path
 *
 * @return string
 */
function slash_to_directory_separator($path)
{
    return str_replace(['/', '\\'], DS, $path);
}
