<?php

if (defined('DEVELOPMENT') && DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

/**
 * @package z20
 * @link http://joshua.lylejohnson.us/z20
 * @author Joshua L. Johnson <josh@ua1.us>
 * @copyright Copyright 2013-2016, Joshua L. Johnson
 * @license MIT
 */
set_exception_handler('z20Error');

/**
 * A function that is used to handle thrown Exceptions.
 *
 * @param strong $error The error
 */
function z20Error($error) {
    header('HTTP/1.1 500 Internal Server Error', 500);
    if (defined('DEVELOPMENT') && DEVELOPMENT) {
        if (php_sapi_name() == 'cli') {
            exit('Z20 CRITICAL ERROR ==> ' . $error->getMessage());
        } else {
            $err = '<h1 style="color: red;border-bottom: 1px dotted #000000;padding-bottom: 15px;">Z20 CRITICAL ERROR</h1>';
            $err .= '<h2>' . $error->getMessage() . '</h2>';
            $err .= '<pre>' . $error->getTraceAsString() . '</pre>';
            exit($err);
        }
    } else {
        exit('A temporary server error has occurred. Please check back soon.');
    }
}
