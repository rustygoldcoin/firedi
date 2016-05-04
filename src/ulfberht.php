<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

/**
 * Returns the ulfberht singleton or module object based on what is passed in.
 * @return mixed Module or Ulfberht object
 */
function ulfberht($module = null) {
    if ($module) {
        return ulfberht\core\ulfberht::getInstance()->getModule($module);
    }
    return ulfberht\core\ulfberht::getInstance();
}

/**
 * register the ulfberht\module module
 */
ulfberht()->registerModule('ulfberht\module');