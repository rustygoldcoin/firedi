<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht;

use Exception;

/**
 * The purpose of this class is to provide a global exception handler to help debug
 * applications.
 */
class debug {

    public static $isEnabled = false;

    /**
     * This method is used to enable debugging within your Ulfberht application.
     */
    public static function enable() {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        set_exception_handler(['ulfberht\debug','ulfberhtExceptionHandler']);
        self::$isEnabled = true;
    }

    /**
     * This method is used to determine if debugging has been turned on.
     * @return boolean If debugging has been enabled.
     */
    public static function isEnabled() {
        return self::$isEnabled;
    }

    /**
     * A function that is used to handle thrown Exceptions.
     * @param strong $exception The Exception thown
     */
    public static function ulfberhtExceptionHandler(Exception $exception) {
        header('HTTP/1.1 500 Internal Server Error', 500);
        if (php_sapi_name() == 'cli') {
            exit('ULFBERHT CRITICAL ERROR ==> ' . $exception->getMessage());
        } else {
            $err = '<h1 style="color: red;border-bottom: 1px dotted #000000;padding-bottom: 15px;">ULFBERHT CRITICAL ERROR</h1>';
            $err .= '<h2>' . $exception->getMessage() . '</h2>';
            $err .= '<pre>' . $exception->getTraceAsString() . '</pre>';
            exit($err);
        }
    }

}
