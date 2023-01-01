<?php

namespace App\Components\Magazine\Validation;

use App\Components\Magazine\PdfThumbQueue;
use Intervention\Image\Image;
use Spatie\PdfToImage\Pdf;

class MagazineValidate
{

    public static function validate()
    {
        $result = [
            'title'           => from_post('title'),
            'price'           => (float)from_post('price'),
            'storage_path'    => from_post('storage_path'),
            'image'           => from_post('image'),
            'pdf'             => from_post('pdf'),
            'pdf_total_pages' => (int)from_post('pdf_total_pages'),
        ];

        $id = (int)from_post('id');


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // проверяем логин
        if (empty($result['title'])) {
            validate_error_and_die('Название не может быть пустым');
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // проверяем цену
        if ($result['price'] < 0) {
            validate_error_and_die('Цена должна быть больше или равна 0');
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        if (! $result['storage_path']) {
            $result['storage_path'] = 'magazines' . DS . md5($result['title']);
        }

        $storage_path = STORAGE_PATH . DS . $result['storage_path'];

        if (! is_dir($storage_path)) {
            mkdir($storage_path);
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // обложка
        $image = from_files('image_file');

        if (! $id and empty($image['tmp_name'])) {
            validate_error_and_die('Обложка не может быть пустым');
        }

        if (! empty($image['tmp_name'])) {
            if (! in_array($image['type'], ['image/jpeg', 'image/png'])) {
                validate_error_and_die('Обложка должна быть изображением JPG или PNG');
            }

            // имя и путь к файлу
            if (! $result['image']) {
                $result['image'] = sprintf('%s.%s', md5($result['title']), explode('/', $image['type'])[1]);
            }
            $new_file_path = $storage_path . DS . $result['image'];

            // загружаем файл
            if (! move_uploaded_file($image['tmp_name'], $new_file_path)) {
                validate_error_and_die('Не удалось загрузить обложку');
            }

            // подгоняем размер и сохраняем
            Image::make($new_file_path)->resize(720, 1280)->save($new_file_path);
        }

        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        // PDF
        $pdf = from_files('pdf_file');

        if (! $id and empty($pdf['tmp_name'])) {
            validate_error_and_die('Обложка не может быть пустой');
        }

        if (! empty($pdf['tmp_name'])) {
            if ($pdf['type'] !== 'application/pdf') {
                validate_error_and_die('PDF должен быть PDF файлом');
            }

            // имя и путь к файлу
            if (! $result['pdf']) {
                $result['pdf'] = sprintf('%s.%s', md5($result['title']), explode('/', $pdf['type'])[1]);
            }
            $new_file_path = $storage_path . DS . $result['pdf'];

            // загружаем файл
            if (! move_uploaded_file($pdf['tmp_name'], $new_file_path)) {
                validate_error_and_die('Не удалось загрузить PDF');
            }

            // создаем превью
            $pdf_thumbs                = new Pdf($new_file_path);
            $pages                     = $pdf_thumbs->getNumberOfPages();
            $result['pdf_total_pages'] = $pages;

            // добавляем в очередь CRON задачу на создание превью
            (new PdfThumbQueue())->insert([
                'pdf'          => $result['pdf'],
                'storage_path' => $result['storage_path'],
            ]);


            /*
                        $pdf_thumbs_dir = $storage_path . DS . 'pdf_thumbs';

                        if (! is_dir($pdf_thumbs_dir)) {
                            mkdir($pdf_thumbs_dir);
                        }

                        // удаляем старые превью
                        array_map('unlink', array_filter((array)glob($pdf_thumbs_dir . DS . '*')));

                        for ($i = 1; $i <= $pages; $i++) {
                            $pdf_thumbs->setPage($i)
                                       ->setOutputFormat('jpg')
                                       ->setCompressionQuality(80)
                                       ->width(110)
                                       ->saveImage($pdf_thumbs_dir . DS . $result['pdf'] . '_' . $i . '.jpg');
                        }*/
        }


        // ————————————————————————————————————————————————————————————————————————————————————————————————————————————

        return $result;
    }

}