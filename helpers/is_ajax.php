<?php

if (!function_exists('is_ajax')) {
    /**
     * Checks if the http request is an AJAX call.
     *
     * @return bool
     * @version 0.101
     */
    function is_ajax()
    {
        return (bool)(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'));
    }
}
