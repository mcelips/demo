<?php

namespace App\Services\DB;

use Exception;
use MySQLDump;
use mysqli;

final class DBDumper
{

    /**
     * Максимальное количество хранимых резервных копий
     */
    const MAX_DUMP_AMOUNT = 5;

    /**
     * Запуск создания резервной копии
     *
     * @param string $operation
     * @param string $database_config
     *
     * @return void
     */
    public static function run(string $operation = 'default', string $database_config = 'default'): void
    {
        // конфигурация для подключения к базе данных
        $db_config = config('database.' . $database_config);

        // если конфигурация не найдена
        if (! $db_config) {
            validate_error_and_die('Конфигурация для подключения к базе данных не найдена.');
        }

        // путь к резервной копии
        $dump_path = sprintf(
            '%s/%s_dump_%s.sql.gz',
            self::getDirectoryPath(),
            date('Y-m-d_H-i-s'),
            $operation
        );

        try {
            // запускаем резервное копирование
            $dump = new MySQLDump(
                new mysqli(
                    $db_config['host'],
                    $db_config['username'],
                    $db_config['password'],
                    $db_config['database']
                )
            );
            $dump->save($dump_path);

            // удаляем старые файлы
            self::deleteOldDumps();
        } catch (Exception $e) {
            // если что-то пошло не так
            validate_error_and_die($e->getMessage());
            logger()?->error($e->getMessage());
        }

        render_set_success('Резервная копия БД создана');
    }

    /**
     * Путь к папке с резервными копиями
     *
     * @return string
     */
    protected static function getDirectoryPath(): string
    {
        $directory = slash_to_directory_separator(ROOT . '/var/db_dump/');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $directory;
    }

    /**
     * Удаляет лишние резервные копии
     *
     * @return void
     */
    public static function deleteOldDumps(): void
    {
        // список резервных копий
        $files = glob(self::getDirectoryPath() . '*.sql.gz');

        // если количество файлов превышает максимальное
        if (count($files) > self::MAX_DUMP_AMOUNT) {
            // список файлов для удаления
            $files_for_delete = array_slice($files, 0, count($files) - self::MAX_DUMP_AMOUNT);

            // удаляем выбранные файлы
            array_map('unlink', $files_for_delete);
        }
    }

}