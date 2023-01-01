<?php

namespace App\Services;

class Helpers
{

    /**
     * Подготавливает массив для ответа клиенту: все значения (кроме массива и bool) конвертирует в строки.
     *
     * @param array $array
     *
     * @return array
     */
    public static function prepareArrayForResponse($array)
    {
        if (empty($array) === true) {
            return $array;
        }

        foreach ($array as $key => $value) {
            // если значение является массив, то обрабатываем значения массива
            if (is_array($value) === true) {
                $array[$key] = self::prepareArrayForResponse($value);
                continue;
            }
            // если значение является bool, то пропускаем
            if (is_bool($value) === true) {
                continue;
            }
            // конвертируем значения в строку
            $array[$key] = (string)$value;
        }

        return $array;
    }

    /**
     * Форматирует число с группировкой тысячных
     *
     * @param float  $number
     * @param int    $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     *
     * @return string
     */
    public static function formatNumber(
        $number,
        $decimals = 2,
        $dec_point = '.',
        $thousands_sep = ' '
    )
    {
        $result = number_format($number, $decimals, $dec_point, $thousands_sep);

        if (strpos($result, $dec_point) !== false) {
            $result = rtrim(rtrim($result, '0'), $dec_point);
        }

        if (empty($result)) {
            $result = '0';
        }

        return $result;
    }


    /**
     * Рассчитывает процент от числа.
     *
     * @param float|int $value   Число
     * @param float|int $percent Процент
     * @param bool|int  $round   Округление
     *
     * @return float|int
     */
    public static function calcPercent(
        $value,
        $percent,
        $round = false
    )
    {
        if ($round === false) {
            return $value * ($percent / 100);
        }

        return round($value * ($percent / 100), $round);
    }

    /**
     * Высчитывает количество оставшихся секунд, минут, часов, дней
     *
     * @param int $delay_before_start Время в секундах
     *
     * @return string
     */
    public static function convertTimestampToText($delay_before_start)
    {
        $get_text_delay_names = self::getTimePeriodText();

        // остались секунды
        if ($delay_before_start < 60) {
            return self::pluralForm($delay_before_start, $get_text_delay_names['seconds']);
        }
        // остались минуты
        if ($delay_before_start < 60 * 60) {
            $delay_before_start = round($delay_before_start / 60, 0, PHP_ROUND_HALF_DOWN);

            return self::pluralForm((int)$delay_before_start, $get_text_delay_names['minutes']);
        }
        // остались часы
        if ($delay_before_start < 60 * 60 * 24) {
            $delay_before_start = round($delay_before_start / (60 * 60), 0, PHP_ROUND_HALF_DOWN);

            return self::pluralForm((int)$delay_before_start, $get_text_delay_names['hours']);
        }
        // остались дни
        if ($delay_before_start < 60 * 60 * 24 * 7) {
            $delay_before_start = round($delay_before_start / (60 * 60 * 24), 0, PHP_ROUND_HALF_DOWN);

            return self::pluralForm((int)$delay_before_start, $get_text_delay_names['days']);
        }
        // остались недели
        if ($delay_before_start < 60 * 60 * 24 * 365) {
            $delay_before_start = round($delay_before_start / (60 * 60 * 24 * 7), 0, PHP_ROUND_HALF_DOWN);

            return self::pluralForm((int)$delay_before_start, $get_text_delay_names['weeks']);
        }
        // остались года (максимум 10 лет)
        if ($delay_before_start < 60 * 60 * 24 * 365 * 10) {
            $delay_before_start = round($delay_before_start / (60 * 60 * 24 * 365), 0, PHP_ROUND_HALF_DOWN);

            return self::pluralForm((int)$delay_before_start, $get_text_delay_names['years']);
        }

        return __t('Too big number');
    }

    /**
     * Возвращает текстовые наименования временных периодов для функции склонения слов
     *
     * @return array
     */
    public static function getTimePeriodText()
    {
        return [
            'years'   => ['год', 'года', 'лет'],
            'weeks'   => ['неделя', 'недели', 'недель'],
            'days'    => ['день', 'дня', 'дней'],
            'hours'   => ['час', 'часа', 'часов'],
            'minutes' => ['минута', 'минуты', 'минут'],
            'seconds' => ['секунда', 'секунды', 'секунд'],
        ];
    }

    /**
     * Функция склонения слов после числительных
     *
     * @param int   $number
     * @param array $after
     *
     * @return string
     */
    public static function pluralForm($number, $after)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * Высчитывает количество оставшихся секунд, минут, часов, дней
     *
     * @param int  $timestamp Время в секундах
     * @param bool $without_seconds
     *
     * @return string Оставшееся время: дни, часы, минуты, секунды
     */
    public static function convertTimestampToTextFull($timestamp, $without_seconds = false)
    {
        $get_text_delay_names = self::getTimePeriodText();

        if ($timestamp < 1) {
            return '0';
        }

        // массив для формирования текста
        $text = [];

        // года
        if ($timestamp > 60 * 60 * 24 * 365) {
            $days      = intval($timestamp / (60 * 60 * 24 * 365));
            $text[]    = self::pluralForm($days, $get_text_delay_names['years']);
            $timestamp -= $days * 60 * 60 * 24 * 365;
        }

        // недели
        if ($timestamp > 60 * 60 * 24 * 7) {
            $days      = intval($timestamp / (60 * 60 * 24 * 7));
            $text[]    = self::pluralForm($days, $get_text_delay_names['weeks']);
            $timestamp -= $days * 60 * 60 * 24 * 7;
        }

        // дни
        if ($timestamp > 60 * 60 * 24) {
            $days      = intval($timestamp / (60 * 60 * 24));
            $text[]    = self::pluralForm($days, $get_text_delay_names['days']);
            $timestamp -= $days * 60 * 60 * 24;
        }

        // часы
        if ($timestamp > 60 * 60) {
            $hours     = intval($timestamp / (60 * 60));
            $text[]    = self::pluralForm($hours, $get_text_delay_names['hours']);
            $timestamp -= $hours * 60 * 60;
        }

        // минуты
        if ($timestamp > 60) {
            $minutes   = intval($timestamp / 60);
            $text[]    = self::pluralForm($minutes, $get_text_delay_names['minutes']);
            $timestamp -= $minutes * 60;
        }

        // секунды
        if ($timestamp > 0 and $without_seconds === false) {
            $seconds = intval($timestamp);
            $text[]  = self::pluralForm($seconds, $get_text_delay_names['seconds']);
        }

        return implode(' ', $text);
    }


    /**
     * Разбивает секунды на массив временных периодов [года, недели, дни, часы, минуты, секунды]
     *
     * @param int   $seconds
     * @param array $periods Периоды, на которые необходимо разбить. ['years', 'weeks', 'days', 'hours', 'minutes']
     *
     * @return int[]
     * @version 0.825
     */
    public static function secondsToArray(
        $seconds,
        $periods = ['years', 'weeks', 'days', 'hours', 'minutes']
    )
    {
        $result = [];

        // года
        if (in_array('years', $periods)) {
            $result['years'] = floor($seconds / 31536000);
            $seconds         = $seconds % 31536000;
        }

        // недели
        if (in_array('weeks', $periods)) {
            $result['weeks'] = floor($seconds / 604800);
            $seconds         = $seconds % 604800;
        }

        // дни
        if (in_array('days', $periods)) {
            $result['days'] = floor($seconds / 86400);
            $seconds        = $seconds % 86400;
        }

        // часы
        if (in_array('hours', $periods)) {
            $result['hours'] = floor($seconds / 3600);
            $seconds         = $seconds % 3600;
        }

        // минуты
        $result['minutes'] = floor($seconds / 60);

        // секунды
        $result['seconds'] = $seconds % 60;

        return $result;
    }


    /**
     * Конвертирует в секунды массив временных периодов [года, недели, дни, часы, минуты, секунды]
     *
     * @param int[] $periods
     *
     * @return int
     */
    public static function timePeriodArrayToSeconds($periods)
    {
        $seconds = 0;

        // года
        if (isset($periods['years'])) {
            $seconds += (int)$periods['years'] * 31536000;
        }

        // недели
        if (isset($periods['weeks'])) {
            $seconds += (int)$periods['weeks'] * 604800;
        }

        // дни
        if (isset($periods['days'])) {
            $seconds += (int)$periods['days'] * 86400;
        }

        // часы
        if (isset($periods['hours'])) {
            $seconds += (int)$periods['hours'] * 3600;
        }

        // минуты
        if (isset($periods['minutes'])) {
            $seconds += (int)$periods['minutes'] * 60;
        }

        // секунды
        if (isset($periods['seconds'])) {
            $seconds += (int)$periods['seconds'];
        }

        return $seconds;
    }

}