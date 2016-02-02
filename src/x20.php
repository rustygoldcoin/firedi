<?php

/**
 * @package x20
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

//bootstrap core x20 files
require_once __DIR__ . '/x20/core/x20.php';
require_once __DIR__ . '/x20/core/x20graph.php';
require_once __DIR__ . '/x20/core/x20module.php';
require_once __DIR__ . '/x20/core/x20service.php';


/**
 * Returns the x20 singleton
 * @return x20
 */
function x20() {
    return x20\core\x20::getInstance();
}