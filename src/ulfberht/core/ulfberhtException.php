<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\core;

use Exception;

class ulfberhtException extends Exception {

    /**
     *Redefine the exception so message isn't optional.
     * @param string $message The message of the exception
     * @param integer $code Error code
     * @param object $previous The previous exception
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
