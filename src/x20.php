<?php

/**
 * @package Ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

//bootstrap core ulfberht files
require_once __DIR__ . '/ulfberht/core/ulfberht.php';
require_once __DIR__ . '/ulfberht/core/ulfberhtGraph.php';
require_once __DIR__ . '/ulfberht/core/ulfberhtModule.php';
require_once __DIR__ . '/ulfberht/core/ulfberhtService.php';

//bootstrap module x20 files
require_once __DIR__ . '/ulfberht/module/ulfberht/ulfberht.php';

/**
 * Returns the x20 singleton
 * @return x20
 */
function ulfberht() {
    return ulfberht\core\ulfberht::getInstance();
}