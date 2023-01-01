<?php

namespace App\Components\News\Validation;

use Intervention\Image\Image;

class NewsValidate
{

    public static function validate()
    {
        $storage_path = STORAGE_PATH . DS . 'news';

        $result = [
            'title'       => from_post('title'),
            'text'        => from_post('text'),
            'image_thumb' => from_post('image_thumb'),
            'image'       => from_post('image'),
        ];

        $id = (int)from_post('id');


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // проверяем логин
        if (empty($result['title'])) {
            validate_error_and_die('Название не может быть пустым');
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // превью изображение
        $image_thumb = from_files('image_thumb_file');

        if (! $id and empty($image_thumb['tmp_name'])) {
            validate_error_and_die('Превью изображение не может быть пустым');
        }

        if (! empty($image_thumb['tmp_name'])) {
            if (! in_array($image_thumb['type'], ['image/jpeg', 'image/png'])) {
                validate_error_and_die('Превью изображение должно быть изображением JPG или PNG');
            }

            // имя и путь к файлу
            if (! $result['image_thumb']) {
                $result['image_thumb'] = sprintf('%s_thumb.%s', md5($result['title']), explode('/', $image_thumb['type'])[1]);
            }
            $new_file_path = $storage_path . DS . $result['image_thumb'];

            // загружаем файл
            if (! move_uploaded_file($image_thumb['tmp_name'], $new_file_path)) {
                validate_error_and_die('Не удалось загрузить превью изображение');
            }

            // подгоняем размер и сохраняем
            Image::make($new_file_path)->resize(360, 640)->save($new_file_path);
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // обложка
        $image = from_files('image_file');

        if (! $id and empty($image['tmp_name'])) {
            validate_error_and_die('Основное изображение не может быть пустым');
        }

        if (! empty($image['tmp_name'])) {
            if (! in_array($image['type'], ['image/jpeg', 'image/png'])) {
                validate_error_and_die('Основное изображение должно быть изображением JPG или PNG');
            }

            // имя и путь к файлу
            if (! $result['image']) {
                $result['image'] = sprintf('%s.%s', md5($result['title']), explode('/', $image['type'])[1]);
            }
            $new_file_path = $storage_path . DS . $result['image'];

            // загружаем файл
            if (! move_uploaded_file($image['tmp_name'], $new_file_path)) {
                validate_error_and_die('Не удалось загрузить основное изображение');
            }

            // подгоняем размер и сохраняем
            Image::make($new_file_path)->resize(720, 1280)->save($new_file_path);
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        return $result;
    }

}