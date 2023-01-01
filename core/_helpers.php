<?php
/**
 * Загрузчик вспомогательных функций.
 * Подключает файлы из папки "helpers" на основе переданного массива с именами файлов.
 *
 * @param array       $files     Массив имен нужных файлов.
 *                               Для подключения файла из подпапки в качестве разделителя используйте точку. Пример,
 *                               значение 'debug.dump' подключит файл 'dump.php' из подпапки 'debug'.
 *                               Если в качестве имени файла передать *, то подключатся все файлы из папки. Например,
 *                               'debug.*' подключит все файлы из подпапки 'debug'.
 * @param string|null $directory Путь к другой папке, отличной от ROOT/helpers/
 */
function helpers($files, $directory = null)
{
    // Полный путь к папке со вспомогательными функциями
    if ($directory === null) {
        $directory = ROOT . DS . 'helpers' . DS;
    } else {
        // Заменяем / на DIRECTORY_SEPARATOR
        $directory = rtrim(str_replace('/', DS, $directory), DS) . DS;

        // Если указан относительный путь
        $directory = strpos($directory, ROOT) === false
            ? ROOT . DS . $directory
            : $directory;
    }

    // Подключаем файлы
    foreach ($files as $file) {
        // Заменяем . на DIRECTORY_SEPARATOR
        $file_name = str_replace('.', DS, $file);

        // Удаляем слэши в начале и конце строки, заменяем слэш на DIRECTORY_SEPARATOR
        $file_name = str_replace('/', DS, trim($file_name, '\\/'));

        // Если в качестве имени файла указана *, то подключаем все файлы из запрашиваемой директории
        if ('*' === substr($file_name, -1)) {
            // Формируем полный путь к подкаталогу
            $subdirectory = $directory . rtrim($file_name, '*');

            // Получаем файлы директории
            $all_files = array_diff(scandir($subdirectory), ['..', '.']);

            // Подключаем файлы поддиректории
            foreach ($all_files as $file_name) {
                // Полный путь к файлу
                $file_path = $subdirectory . $file_name;

                // Если директория, то подключаем файлы рекурсивно
                if (is_dir($file_path) === true) {
                    helpers([$file_name . '.*'], $subdirectory);
                } else {
                    // Подключаем файл
                    helpers_require_file($file_path);
                }
            }
        } else {
            // Полный путь к файлу
            $file_path = $directory . $file_name . '.php';

            // Подключаем файл
            helpers_require_file($file_path);
        }
    }
}

/**
 * Проверяет подключен ли запрашиваемый файл ранее. Если нет, то подключает.
 *
 * @param string $file_path
 */
function helpers_require_file($file_path)
{
    // Если файл существует и не был подключен ранее, то подключаем файл
    if (file_exists($file_path) and false === in_array($file_path, get_included_files())) {
        require_once $file_path;
    }
}

/**
 * Возвращает массив списка подключенных хелперов.
 *
 * @return array
 */
function helpers_included()
{
    $files = [];
    $directory = ROOT . DS . 'helpers' . DS;

    foreach (get_included_files() as $file) {
        if (strpos($file, $directory) !== false) {
            $files[] = str_replace($directory, '', $file);
        }
    }
    return $files;
}
