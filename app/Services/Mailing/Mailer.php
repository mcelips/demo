<?php

namespace App\Services\Mailing;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

final class Mailer
{
    /**
     * Отправляет письмо на E-mail
     *
     * @param string                          $email
     * @param string                          $subject
     * @param string                          $message
     * @param MailerAttachmentCollection|null $attachmentCollection
     * @param bool                            $without_error
     *
     * @return bool
     */
    public static function send(
        string                     $email,
        string                     $subject,
        string                     $message,
        MailerAttachmentCollection $attachmentCollection = null,
        bool                       $without_error = false
    ): bool
    {
        try {
            if (empty($email) === true) {
                validate_error_and_die(__t('E-mail not specified to send email.'));
            }

            // получаем настройки
            $config = config('mail');

            // если нет настроек
            if (! $config or empty($config)) {
                validate_error_and_die(
                    __t(
                        'Server error. Failed to send mail to %email%',
                        ['email' => $email]
                    )
                );
            }

            // настраиваем PHPMailer
            $php_mailer = new PHPMailer;

            // отладка отправки письма
            if ($config['smtp']['debug']) {
                $php_mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            }

            // использование шифрования
            if ($config['smtp']['encryption']) {
                $php_mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $php_mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            //
            $php_mailer->SMTPAutoTLS = $config['smtp']['autoTLS'];

            // подключение
            $php_mailer->isSMTP();
            $php_mailer->Host = $config['smtp']['host'];
            $php_mailer->Port = $config['smtp']['port'];

            // авторизация
            $php_mailer->SMTPAuth = true;
            $php_mailer->Username = $config['smtp']['username'];
            $php_mailer->Password = $config['smtp']['password'];
            $php_mailer->CharSet  = $php_mailer::CHARSET_UTF8;

            // указываем адрес, тему, сообщение
            $php_mailer->setFrom($config['from']['address'], $config['from']['name']);
            $php_mailer->addAddress($email);
            $php_mailer->Subject = $subject;
            $php_mailer->msgHTML($message);

            // прикрепленные файлы
            if ($attachmentCollection and $attachmentCollection->notEmpty()) {
                foreach ($attachmentCollection->get() as $attachment) {
                    $php_mailer->addAttachment($attachment['path'], $attachment['name']);
                }
            }

            // отправляем письмо
            if ($php_mailer->send() === false) {
                if ($without_error === true) {
                    return false;
                }
                validate_error_and_die(__t('Failed to send Email. Contact to support.'));
            }

            // при успешной отправке
            return true;
        } catch (Exception $exception) {
            if ($without_error === true) {
                return false;
            }
            validate_error_and_die(
                __t(
                    'Failed to send Email. Contact to support. Details: %details%',
                    ['details' => $exception->getMessage()]
                )
            );
        }

        return false;
    }

}