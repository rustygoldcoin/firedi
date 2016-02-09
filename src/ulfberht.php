<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

//bootstrap core ulfberht files
require_once __DIR__ . '/ulfberht/core/ulfberht.php';
require_once __DIR__ . '/ulfberht/core/graph.php';
require_once __DIR__ . '/ulfberht/core/module.php';
require_once __DIR__ . '/ulfberht/core/service.php';

//bootstrap module ulfberht files
require_once __DIR__ . '/ulfberht/module/ulfberht/ulfberht.php';

/**
 * Returns the ulfberht singleton
 * @return ulfberht
 */
function ulfberht() {
    return ulfberht\core\ulfberht::getInstance();
}