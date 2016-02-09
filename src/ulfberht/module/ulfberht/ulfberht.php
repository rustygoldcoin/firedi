<?php
namespace ulfberht\module;

use ulfberht\core\module;

class ulfberht extends module {
    
    public function __construct() {
        //register config service
        $this->registerSingleton('ulfberht\module\ulfberht\config');
    }
    
}

//include clas definitions and register module
require_once __DIR__ . '/service/config.php';
ulfberht()->registerModule('ulfberht\module\ulfberht');