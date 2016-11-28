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
 * @return mixed Module or Ulfberht object
 */
function ulfberht($service = null) {
    return ulfberht\core\ulfberht::instance();
}
