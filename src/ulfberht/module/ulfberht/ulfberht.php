<?php

/**
 * @package ulfberht
 * @author Joshua L. Johnson <josh@ua1.us>
 * @link http://labs.ua1.us
 * @copyright Copyright 2016, Joshua L. Johnson
 * @license MIT
 */

namespace ulfberht\module;

use ulfberht\core\module;

class ulfberht extends module {
    
    public function __construct() {
        //register config service
        $this->registerSingleton('ulfberht\module\ulfberht\config');
        $this->registerSingleton('ulfberht\module\ulfberht\router');
    }
    
}

//include clas definitions and register module
require_once __DIR__ . '/service/config.php';
require_once __DIR__ . '/service/router.php';
ulfberht()->registerModule('ulfberht\module\ulfberht');