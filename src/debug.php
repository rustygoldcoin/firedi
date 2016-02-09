<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

set_exception_handler('ulfberhtError');

/**
 * A function that is used to handle thrown Exceptions.
 *
 * @param strong $error The error
 */
function ulfberhtError($error) {
    header('HTTP/1.1 500 Internal Server Error', 500);
    if (php_sapi_name() == 'cli') {
        exit('ULFBERHT CRITICAL ERROR ==> ' . $error->getMessage());
    } else {
        $err = '<h1 style="color: red;border-bottom: 1px dotted #000000;padding-bottom: 15px;">ULFBERHT CRITICAL ERROR</h1>';
        $err .= '<h2>' . $error->getMessage() . '</h2>';
        $err .= '<pre>' . $error->getTraceAsString() . '</pre>';
        exit($err);
    }
}
