<?php

namespace App\Controllers\Api;

use App\Components\Competition\Competition;
use App\Controllers\Controller;
use App\Services\Mailing\Mailer;
use App\Services\Mailing\MailerAttachmentCollection;

class CompetitionController extends Controller
{

    /**
     * Список конкурсов
     *
     * @return void
     */
    public function all(): void
    {
        $response = [];
        if ($competitions = (new Competition())->get()) {
            foreach ($competitions as $competition) {
                $response[] = [
                    'id'          => $competition['id'],
                    'title'       => $competition['title'],
                    'text'        => $competition['text'],
                    'image_thumb' => get_url('/storage/competitions/' . $competition['image_thumb']),
                    'image'       => get_url('/storage/competitions/' . $competition['image']),
                ];
            }
        }
        json_api_response($response);
    }


    /**
     * Отправка данных из формы на почтовый адрес
     *
     * @return void
     */
    public function form(): void
    {
        // данные из формы
        $photo_file = from_files('photo');
        $name       = from_post('name');

        // добавляем файлы в коллекцию
        $mailerAttachmentCollection = new MailerAttachmentCollection();
        $mailerAttachmentCollection->add($photo_file['tmp_name'], $photo_file['name']);

        // отправляем письмо
        $send_result = Mailer::send(
            '',
            'Subject',
            sprintf('Test name is <b>%s</b>', $name),
            $mailerAttachmentCollection
        );

        // ответ киенту
        if ($send_result) {
            json_api_response('OK');
        }

        json_api_response('Failed');
    }

}