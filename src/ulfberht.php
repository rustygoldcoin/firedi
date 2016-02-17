<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

/**
 * Returns the ulfberht singleton
 * @return ulfberht
 */
function ulfberht() {
    return ulfberht\core\ulfberht::getInstance();
}

//register ulfberht module
ulfberht()->registerModule('ulfberht\module\ulfberht');