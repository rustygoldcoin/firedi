<?php
/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

/**
 * Returns the ulfberht singleton or module object based on what is passed in.
 * @return mixed Ulfberht object
 */
function ulfberht() {
    return ulfberht\core\ulfberht::instance();
}
