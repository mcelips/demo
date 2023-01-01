<?php

function log_save($message, $type = 'error')
{
    $date = date('Y-m-d H:i:s');

    $type = ucfirst($type);

    $log = "$date\n$type | $message";

    $dir = ROOT . '/tmp/logs';

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($dir . '/log.log', $log . PHP_EOL, FILE_APPEND);
}


/**
 * Error log message
 *
 * @param $message
 */
function log_error($message)
{
    log_save($message, 'error');
}


/**
 * Info log message
 *
 * @param $message
 */
function log_info($message)
{
    log_save($message, 'info');
}


/**
 * Success log message
 *
 * @param $message
 */
function log_success($message)
{
    log_save($message, 'success');
}


/**
 * Warning log message
 *
 * @param $message
 */
function log_warning($message)
{
    log_save($message, 'warning');
}
